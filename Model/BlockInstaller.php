<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Firegento\ContentProvisioning\Model\Command\ApplyBlockEntry;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryList;
use Firegento\ContentProvisioning\Model\Validator\CanApplyBlockEntry;
use Psr\Log\LoggerInterface;

class BlockInstaller
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GetBlockEntryList
     */
    private $getAllBlockEntries;

    /**
     * @var CanApplyBlockEntry
     */
    private $canApplyBlockEntry;

    /**
     * @var ApplyBlockEntry
     */
    private $applyBlockEntry;

    /**
     * @param LoggerInterface $logger
     * @param GetBlockEntryList $getAllBlockEntries
     * @param CanApplyBlockEntry $canApplyBlockEntry
     * @param ApplyBlockEntry $applyBlockEntry
     */
    public function __construct(
        LoggerInterface $logger,
        GetBlockEntryList $getAllBlockEntries,
        CanApplyBlockEntry $canApplyBlockEntry,
        ApplyBlockEntry $applyBlockEntry
    ) {
        $this->logger = $logger;
        $this->getAllBlockEntries = $getAllBlockEntries;
        $this->canApplyBlockEntry = $canApplyBlockEntry;
        $this->applyBlockEntry = $applyBlockEntry;
    }

    /**
     * Apply all configured CMS page changes
     *
     * @return void
     */
    public function install(): void
    {
        foreach ($this->getAllBlockEntries->get() as $blockEntry) {
            try {
                if ($this->canApplyBlockEntry->execute($blockEntry)) {
                    $this->applyBlockEntry->execute($blockEntry);
                }
            } catch (\Exception $exception) {
                $this->logger->error(sprintf(
                    'An error appeared while applying cms block content: %s',
                    $exception->getMessage()
                ), [
                    'block-data' => $blockEntry->getData(),
                    'trace' => $exception->getTrace(),
                ]);
            }
        }
    }
}