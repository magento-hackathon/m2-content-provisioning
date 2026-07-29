<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see https://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 *
 * @copyright  Copyright (c) 2024 TechDivision GmbH (https://www.techdivision.com)
 * @author     TechDivision Team Zero <zero@techdivision.com>
 * @link       https://www.techdivision.com/
 */

namespace Firegento\ContentProvisioning\Cron;

use Firegento\ContentProvisioning\Model\DTO\CmsBlockDto;
use Firegento\ContentProvisioning\Model\Config\Source\EmailFrequency;
use Firegento\ContentProvisioning\Service\EmailService;
use Firegento\ContentProvisioning\Service\BlockQueryService;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Escaper;

class InconsistenciesInCmsBlock
{
    public const EMAIL_SUBJECT = 'CMS block inconsistency report';
    public const CONFIG_PATH_ENABLED = 'trans_email/ident_content_provisioning/enabled';
    public const CONFIG_PATH_SENDER_EMAIL = 'trans_email/ident_general/email';
    public const CONFIG_PATH_RECIPIENT_EMAIL = 'trans_email/ident_content_provisioning/email';
    public const CONFIG_PATH_EMAIL_FREQUENCY = 'trans_email/ident_content_provisioning/email_frequency';
    public const CACHE_KEY_LAST_EMAIL_SENT = 'content_provisioning_last_email_sent';

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var ScopeConfigInterface */
    private ScopeConfigInterface $scopeConfig;

    /** @var EmailService */
    private EmailService $emailService;

    /** @var BlockQueryService */
    private BlockQueryService $blockQueryService;

    /** @var CacheInterface */
    private CacheInterface $cache;

    /** @var Escaper */
    private Escaper $escaper;

    /**
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param EmailService $emailService
     * @param BlockQueryService $blockQueryService
     * @param CacheInterface $cache
     * @param Escaper $escaper
     */
    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        EmailService $emailService,
        BlockQueryService $blockQueryService,
        CacheInterface $cache,
        Escaper $escaper
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->emailService = $emailService;
        $this->blockQueryService = $blockQueryService;
        $this->cache = $cache;
        $this->escaper = $escaper;
    }

    /**
     * Checks for changed block identifiers and sends an email if changes are detected.
     * @return void
     */
    public function execute(): void
    {
        if (!$this->canSendEmail()) {
            return;
        }

        $filesContentInformation = $this->blockQueryService->getFilesContentInformation();
        $changedBlocksIdentifier = $this->blockQueryService->getChangedBlockIdentifiers($filesContentInformation);

        if ($changedBlocksIdentifier) {
            $emailMessage = $this->getValuesForEmail($changedBlocksIdentifier);
            $this->sendEmail($emailMessage);
            $this->updateLastEmailSentTimestamp();
        } else {
            $this->logger->info('No changed blocks found.');
        }
    }

    /**
     * Transform array values into an HTML string to be displayed in the email
     * @param CmsBlockDto[] $entries
     * @return string
     */
    private function getValuesForEmail(array $entries): string
    {
        // Get the shop URL to identify which system the email comes from
        $shopUrl = $this->scopeConfig->getValue('web/secure/base_url') ?:
                   $this->scopeConfig->getValue('web/unsecuresecure/base_url') ?:
                   '#UnknownShopUrl';

        $emailContent = '<html><body>';
        $emailContent .= '<p>Hello,<br>';
        $emailContent .= 'This is an automated report from <a href="' .
                         $this->escaper->escapeHtml($shopUrl) . '">' .
                         $this->escaper->escapeHtml($shopUrl) . '</a>.</p>';
        $emailContent .= '<p>The following CMS blocks have differing content between code & DB:</p>';

        $emailContent .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
        $emailContent .= '<thead>';
        $emailContent .= '<tr style="background-color: #f0f0f0;">';
        $emailContent .= '<th style="text-align: left; padding: 8px;">Key</th>';
        $emailContent .= '<th style="text-align: left; padding: 8px;">Identifier</th>';
        $emailContent .= '<th style="text-align: left; padding: 8px;">Stores</th>';
        $emailContent .= '<th style="text-align: left; padding: 8px;">Maintained</th>';
        $emailContent .= '<th style="text-align: left; padding: 8px;">Active</th>';
        $emailContent .= '<th style="text-align: left; padding: 8px;">Title</th>';
        $emailContent .= '<th style="text-align: left; padding: 8px;">Block ID(s) in DB</th>';
        $emailContent .= '</tr>';
        $emailContent .= '</thead>';
        $emailContent .= '<tbody>';

        foreach ($entries as $entry) {
            $storeCodes = $entry->getStoreCodes();
            $dbBlockIds = $this->blockQueryService->getDbBlockIds($entry->getKey());
            $emailContent .= '<tr>';
            $emailContent .= '<td style="padding: 8px;">' . $this->escaper->escapeHtml($entry->getKey()) . '</td>';
            $emailContent .= '<td style="padding: 8px;">' .
                $this->escaper->escapeHtml($entry->getIdentifier()) . '</td>';
            $emailContent .= '<td style="padding: 8px;">' .
                             $this->escaper->escapeHtml(implode(', ', $storeCodes)) . '</td>';
            $emailContent .= '<td style="padding: 8px;">' .
                             $this->escaper->escapeHtml($entry->isMaintained() ? 'yes' : 'no') . '</td>';
            $emailContent .= '<td style="padding: 8px;">' .
                             $this->escaper->escapeHtml($entry->isActive() ? 'yes' : 'no') . '</td>';
            $emailContent .= '<td style="padding: 8px;">' . $this->escaper->escapeHtml($entry->getTitle()) . '</td>';
            $emailContent .= '<td style="padding: 8px;">' .
                             $this->escaper->escapeHtml(implode(',', $dbBlockIds)) . '</td>';
            $emailContent .= '</tr>';
        }

        $emailContent .= '</tbody>';
        $emailContent .= '</table>';

        // Add closing formula with recommendation
        $emailContent .= '<br>';
        $emailContent .= '<p>Recommendation:<br>';
        $emailContent .= 'The code should probably be adjusted to match the state in the database ';
        $emailContent .= 'so that all systems have consistent content.</p>';

        // Add friendly farewell
        $emailContent .= '<br>';
        $emailContent .= 'Best regards,<br>';
        $emailContent .= 'Your <a href="https://github.com/magento-hackathon/m2-content-provisioning">' .
                         'firegento/magento2-content-provisioning</a>!';

        $emailContent .= '</body></html>';

        return $emailContent;
    }

    /**
     * @param string $messageText
     * @return void
     */
    private function sendEmail(string $messageText): void
    {
        // Get shop name and create dynamic subject
        $shopName = $this->scopeConfig->getValue('general/store_information/name') ?: 'Shop';
        $subject = $shopName . ': ' . self::EMAIL_SUBJECT;

        $this->emailService->sendEmail(
            $messageText,
            $subject,
            self::CONFIG_PATH_SENDER_EMAIL,
            self::CONFIG_PATH_RECIPIENT_EMAIL
        );
    }

    /**
     * Check if an email can be sent based on the configured frequency
     * @return bool
     */
    private function canSendEmail(): bool
    {
        $enabled = $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED);

        if (!$enabled) {
            return false;
        }

        $lastEmailSent = $this->cache->load(self::CACHE_KEY_LAST_EMAIL_SENT);

        if (!$lastEmailSent) {
            return true; // No previous email sent, allow sending
        }

        $lastEmailTimestamp = (int)$lastEmailSent;
        $nextAllowedSendTime = $this->getNextAllowedSendTime($lastEmailTimestamp);

        return time() >= $nextAllowedSendTime;
    }

    /**
     * Update the timestamp of the last sent email in cache
     * @return void
     */
    private function updateLastEmailSentTimestamp(): void
    {
        $this->cache->save(
            (string)time(),
            self::CACHE_KEY_LAST_EMAIL_SENT,
            [],
            null // No expiration, we want to keep this indefinitely
        );
    }

    /**
     * Get the next allowed send time based on the configured frequency
     * @param int $lastEmailTimestamp
     * @return int
     */
    private function getNextAllowedSendTime(int $lastEmailTimestamp): int
    {
        $frequency = $this->scopeConfig->getValue(self::CONFIG_PATH_EMAIL_FREQUENCY);

        return match ($frequency) {
            EmailFrequency::DAILY => strtotime('+1 day', $lastEmailTimestamp),
            EmailFrequency::WEEKLY => strtotime('+1 week', $lastEmailTimestamp),
            EmailFrequency::MONTHLY => strtotime('+1 month', $lastEmailTimestamp),
            EmailFrequency::QUARTERLY => strtotime('+3 months', $lastEmailTimestamp),
            EmailFrequency::YEARLY => strtotime('+1 year', $lastEmailTimestamp),
            default => strtotime('+1 month', $lastEmailTimestamp),
        };
    }
}
