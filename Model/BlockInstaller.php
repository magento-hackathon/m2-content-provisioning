<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Exception;
use Firegento\ContentProvisioning\Model\Command\ApplyBlockEntry;
use Firegento\ContentProvisioning\Model\Command\ApplyMediaFiles;
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
     * @var ApplyMediaFiles
     */
    private $applyMediaFiles;

    /**
     * @param LoggerInterface $logger
     * @param GetBlockEntryList $getAllBlockEntries
     * @param CanApplyBlockEntry $canApplyBlockEntry
     * @param ApplyBlockEntry $applyBlockEntry
     * @param ApplyMediaFiles $applyMediaFiles
     */
    public function __construct(
        LoggerInterface $logger,
        GetBlockEntryList $getAllBlockEntries,
        CanApplyBlockEntry $canApplyBlockEntry,
        ApplyBlockEntry $applyBlockEntry,
        ApplyMediaFiles $applyMediaFiles
    ) {
        $this->logger = $logger;
        $this->getAllBlockEntries = $getAllBlockEntries;
        $this->canApplyBlockEntry = $canApplyBlockEntry;
        $this->applyBlockEntry = $applyBlockEntry;
        $this->applyMediaFiles = $applyMediaFiles;
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
                    $this->applyMediaFiles->execute($blockEntry);
                }
            } catch (Exception $exception) {
                $this->logger->error(
                    sprintf(
                        'An error appeared while applying cms block content: %s',
                        $exception->getMessage()
                    ),
                    [
                        'block-data' => $blockEntry->getData(),
                        'trace' => $exception->getTrace(),
                    ]
                );
            }
        }
    }
}
