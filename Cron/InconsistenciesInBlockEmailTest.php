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

use Firegento\ContentProvisioning\Model\Console\BlockListCommand;
use Magento\Framework\Mail\Message;
use Psr\Log\LoggerInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class InconsistenciesInBlockEmailTest
{
    public const EMAIL_SUBJECT = "Inconsistencies in CMS block";

    public const SENDER_EMAIL = 'trans_email/ident_general/email';

    public const RECIPIENT_EMAIL = 'trans_email/ident_content_provisioning/email';

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var BlockListCommand
     */
    private BlockListCommand $blockList;

    /**
     * @var TransportInterfaceFactory
     */
    private TransportInterfaceFactory $mailTransportFactory;

    /**
     * @var Message
     */
    private Message $message;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param LoggerInterface $logger
     * @param BlockListCommand $blockList
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param Message $message
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        LoggerInterface $logger,
        BlockListCommand $blockList,
        TransportInterfaceFactory $mailTransportFactory,
        Message $message,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->blockList = $blockList;
        $this->mailTransportFactory = $mailTransportFactory;
        $this->message = $message;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * call sendEmail function or write to system.log
     *
     * @return void
     * @throws \Exception
     */
    public function execute(): void
    {
        $changedBlocks = $this->blockList->getChangedEntries();
        if ($changedBlocks) {
            $emailMessage = $this->blockList->renderValuesForEmail($changedBlocks);
            $this->sendEmail($emailMessage);
        } else {
            $this->logger->info("No changed blocks found.");
        }
    }

    /**
     * @param string $message
     * @return void
     */
    public function sendEmail(string $message): void
    {
        if ($this->hasRecipientEmail()) {
            try {
                $this->message->setFrom($this->scopeConfig->getValue(self::SENDER_EMAIL));
                $this->message->addTo($this->scopeConfig->getValue(self::RECIPIENT_EMAIL));
                $this->message->setSubject(self::EMAIL_SUBJECT);
                $this->message->setBody($message);
                $transport = $this->mailTransportFactory->create(['message' => $this->message]);
                $transport->sendMessage();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->logger->error("Email not sent! No recipient email found.");
        }
    }

    /**
     * Check if there is any email saved in the admin panel
     * @return bool
     */
    public function hasRecipientEmail(): bool
    {
        return $this->scopeConfig->getValue(self::RECIPIENT_EMAIL) ? true : false;
    }
}
