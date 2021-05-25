<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Exception;
use Firegento\ContentProvisioning\Model\Command\ApplyMediaFiles;
use Firegento\ContentProvisioning\Model\Command\ApplyPageEntry;
use Firegento\ContentProvisioning\Model\Query\GetPageEntryList;
use Firegento\ContentProvisioning\Model\Validator\CanApplyPageEntry;
use Psr\Log\LoggerInterface;

class PageInstaller
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GetPageEntryList
     */
    private $getAllPageEntries;

    /**
     * @var ApplyPageEntry
     */
    private $applyPageEntry;

    /**
     * @var CanApplyPageEntry
     */
    private $canApplyPageEntry;

    /**
     * @var ApplyMediaFiles
     */
    private $applyMediaFiles;

    /**
     * @param LoggerInterface $logger
     * @param GetPageEntryList $getAllPageEntries
     * @param ApplyPageEntry $applyPageEntry
     * @param CanApplyPageEntry $canApplyPageEntry
     * @param ApplyMediaFiles $applyMediaFiles
     */
    public function __construct(
        LoggerInterface $logger,
        GetPageEntryList $getAllPageEntries,
        ApplyPageEntry $applyPageEntry,
        CanApplyPageEntry $canApplyPageEntry,
        ApplyMediaFiles $applyMediaFiles
    ) {
        $this->logger = $logger;
        $this->getAllPageEntries = $getAllPageEntries;
        $this->applyPageEntry = $applyPageEntry;
        $this->canApplyPageEntry = $canApplyPageEntry;
        $this->applyMediaFiles = $applyMediaFiles;
    }

    /**
     * Apply all configured CMS page changes
     *
     * @return void
     */
    public function install(): void
    {
        foreach ($this->getAllPageEntries->get() as $pageEntry) {
            try {
                if ($this->canApplyPageEntry->execute($pageEntry)) {
                    $this->applyPageEntry->execute($pageEntry);
                    $this->applyMediaFiles->execute($pageEntry);
                }
            } catch (Exception $exception) {
                $this->logger->error(sprintf(
                    'An error appeared while applying cms page content: %s',
                    $exception->getMessage()
                ), [
                    'page-data' => $pageEntry->getData(),
                    'trace' => $exception->getTrace(),
                ]);
            }
        }
    }
}
