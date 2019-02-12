<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Firegento\ContentProvisioning\Model\Command\ApplyPageEntry;
use Firegento\ContentProvisioning\Model\Query\GetPageEntryList;
use Firegento\ContentProvisioning\Model\Validator\CanApplyPageEntry;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class PageInstaller
{
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param GetPageEntryList $getAllPageEntries
     * @param ApplyPageEntry $applyPageEntry
     * @param CanApplyPageEntry $canApplyPageEntry
     * @param LoggerInterface $logger
     */
    public function __construct(
        GetPageEntryList $getAllPageEntries,
        ApplyPageEntry $applyPageEntry,
        CanApplyPageEntry $canApplyPageEntry,
        LoggerInterface $logger
    ) {
        $this->getAllPageEntries = $getAllPageEntries;
        $this->applyPageEntry = $applyPageEntry;
        $this->canApplyPageEntry = $canApplyPageEntry;
        $this->logger = $logger;
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
                }
            } catch (\Exception $exception) {
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