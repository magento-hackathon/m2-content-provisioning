<?php
declare(strict_types=1);

/**
 * Copyright (c) 2025 TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see https://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 *
 * @copyright  Copyright (c) 2025 TechDivision GmbH (https://www.techdivision.com)
 * @author     TechDivision Team Zero <zero@techdivision.com>
 * @link       https://www.techdivision.com/
 */

namespace Firegento\ContentProvisioning\Service;

use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\Mail\MimeMessageFactory;
use Magento\Framework\Mail\AddressFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class EmailService
{
    /** @var TransportInterfaceFactory */
    private TransportInterfaceFactory $mailTransportFactory;
    /** @var EmailMessageInterfaceFactory */
    private EmailMessageInterfaceFactory $messageFactory;
    /** @var MimePartInterfaceFactory */
    private MimePartInterfaceFactory $mimePartFactory;
    /** @var MimeMessageFactory */
    private MimeMessageFactory $mimeMessageFactory;
    /** @var AddressFactory */
    private AddressFactory $addressFactory;
    /** @var ScopeConfigInterface */
    private ScopeConfigInterface $scopeConfig;
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /**
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param EmailMessageInterfaceFactory $messageFactory
     * @param MimePartInterfaceFactory $mimePartFactory
     * @param MimeMessageFactory $mimeMessageFactory
     * @param AddressFactory $addressFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportInterfaceFactory $mailTransportFactory,
        EmailMessageInterfaceFactory $messageFactory,
        MimePartInterfaceFactory $mimePartFactory,
        MimeMessageFactory $mimeMessageFactory,
        AddressFactory $addressFactory,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->mailTransportFactory = $mailTransportFactory;
        $this->messageFactory = $messageFactory;
        $this->mimePartFactory = $mimePartFactory;
        $this->mimeMessageFactory = $mimeMessageFactory;
        $this->addressFactory = $addressFactory;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * @param string $messageText
     * @param string $subject
     * @param string $senderConfigPath
     * @param string $recipientConfigPath
     * @return void
     */
    public function sendEmail(
        string $messageText,
        string $subject,
        string $senderConfigPath,
        string $recipientConfigPath
    ): void {
        if (!$this->hasRecipientEmail($recipientConfigPath)) {
            $this->logger->error('Email not sent! No recipient email found.');
            return;
        }

        try {
            $mimePart = $this->mimePartFactory->create(
                [
                    'content' => $messageText,
                    'type' => 'text/html',
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
                    'email' => $this->scopeConfig->getValue($senderConfigPath),
                    'name' => ''
                ]
            );

            $to = $this->addressFactory->create(
                [
                    'email' => $this->scopeConfig->getValue($recipientConfigPath),
                    'name' => ''
                ]
            );

            $data = $this->messageFactory->create(
                [
                    'subject' => $subject,
                    'from' => [$from],
                    'to' => [$to],
                    'body' => $message
                ]
            );

            $transport = $this->mailTransportFactory->create(
                ['message' => $data]
            );
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param string $recipientConfigPath
     * @return bool
     */
    public function hasRecipientEmail(string $recipientConfigPath): bool
    {
        return !empty(trim((string) $this->scopeConfig->getValue($recipientConfigPath)));
    }
}
