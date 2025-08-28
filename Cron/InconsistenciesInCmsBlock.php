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
use Firegento\ContentProvisioning\Model\Query\GetChangedBlockIdentifiers;
use Firegento\ContentProvisioning\Model\Query\GetFilesContentInformation;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\Mail\TransportInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\MimePartInterface;
use Magento\Framework\Mail\MimeMessageFactory;
use Magento\Framework\Mail\AddressFactory;

class InconsistenciesInCmsBlock
{
    public const EMAIL_SUBJECT = "Inconsistencies in CMS block";

    public const SENDER_EMAIL = 'trans_email/ident_general/email';

    public const RECIPIENT_EMAIL = 'trans_email/ident_content_provisioning/email';

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var TransportInterfaceFactory
     */
    private TransportInterfaceFactory $mailTransportFactory;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var GetChangedBlockIdentifiers
     */
    private GetChangedBlockIdentifiers $getChangedBlockIdentifier;

    /**
     * @var GetFilesContentInformation
     */
    private GetFilesContentInformation $getFilesContentInformation;

    /**
     * @var EmailMessageInterfaceFactory
     */
    private EmailMessageInterfaceFactory $messageFactory;

    /**
     * @var MimePartInterfaceFactory
     */
    private MimePartInterfaceFactory $mimePartFactory;

    /**
     * @var AddressFactory
     */
    private AddressFactory $addressFactory;

    /**
     * @var MimeMessageFactory
     */
    private MimeMessageFactory $mimeMessageFactory;

    /**
     * @param LoggerInterface $logger
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param EmailMessageInterfaceFactory $messageFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param GetFilesContentInformation $getFilesContentInformation
     * @param GetChangedBlockIdentifiers $getChangedBlockIdentifier
     * @param MimePartInterfaceFactory $mimePartFactory
     * @param MimeMessageFactory $mimeMessageFactory
     * @param AddressFactory $addressFactory
     */
    public function __construct(
        LoggerInterface $logger,
        TransportInterfaceFactory $mailTransportFactory,
        EmailMessageInterfaceFactory $messageFactory,
        ScopeConfigInterface $scopeConfig,
        GetFilesContentInformation $getFilesContentInformation,
        GetChangedBlockIdentifiers $getChangedBlockIdentifier,
        MimePartInterfaceFactory $mimePartFactory,
        MimeMessageFactory $mimeMessageFactory,
        AddressFactory $addressFactory
    ) {
        $this->logger = $logger;
        $this->mailTransportFactory = $mailTransportFactory;
        $this->messageFactory =$messageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->getFilesContentInformation = $getFilesContentInformation;
        $this->getChangedBlockIdentifier = $getChangedBlockIdentifier;
        $this->mimePartFactory = $mimePartFactory;
        $this->mimeMessageFactory = $mimeMessageFactory;
        $this->addressFactory = $addressFactory;
    }

    /**
     * call sendEmail function or write to system.log
     *
     * @return void
     * @throws \Exception
     */
    public function execute(): void
    {
        $filesContentInformation = $this->getFilesContentInformation->execute();
        $changedBlocksIdentifier = $this->getChangedBlockIdentifier->execute($filesContentInformation);
        if ($changedBlocksIdentifier) {
            $emailMessage = $this->getValuesForEmail($changedBlocksIdentifier);
            $this->sendEmail($emailMessage);
        } else {
            $this->logger->info("No changed blocks found.");
        }
    }

    /**
     * Transform array values into a string to be displayed in the email
     * @param CmsBlockDto[] $entries
     * @return string
     */
    private function getValuesForEmail(array $entries): string
    {
        $entriesList = [];
        foreach ($entries as $entry) {
            $entriesList[] = "key: " . $entry->getKey() . ", identifier: " . $entry->getIdentifier();
        }
        return implode(PHP_EOL, $entriesList);
    }

    /**
     * @param string $messageText
     * @return void
     */
    private function sendEmail(string $messageText): void
    {
        if ($this->hasRecipientEmail()) {
            try {
                $mimePart = $this->mimePartFactory->create(
                    [
                        'content' => $messageText,
                        'type' => 'text/plain',
                        'charset' => 'utf-8'
                    ]
                );

                $message = $this->mimeMessageFactory->create(
                    [
                        'parts' => [$mimePart]
                    ]
                );

                $from = $this->addressFactory->create(
                    [
                        'email' => $this->scopeConfig->getValue(self::SENDER_EMAIL),
                        'name' => ''
                    ]
                );

                $to = $this->addressFactory->create(
                    [
                        'email' => $this->scopeConfig->getValue(self::RECIPIENT_EMAIL),
                        'name' => ''
                    ]
                );

                $data = $this->messageFactory->create(
                    [
                        'subject' => self::EMAIL_SUBJECT,
                        'from' => [$from],
                        'to' => [$to],
                        'body' => $message
                    ]
                );

                /** @var TransportInterface $transport */
                $transport = $this->mailTransportFactory->create(['message' => $data]);
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
    private function hasRecipientEmail(): bool
    {
        return $this->scopeConfig->isSetFlag(self::RECIPIENT_EMAIL) ? true : false;
    }
}
