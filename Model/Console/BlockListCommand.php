<?php

declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Console;

use Firegento\ContentProvisioning\Model\Query\GetBlockEntryByKey;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryList as GetBlockEntryList;
use Firegento\ContentProvisioning\Model\Query\GetBlocksByBlockEntry as GetBlocksByBlockEntry;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File as FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Filesystem\Driver\File;
use Firegento\ContentProvisioning\Model\Query\GetContentProvisioningFiles;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BlockListCommand extends Command
{
    public const PARAM_CHANGED_ONLY = 'changed-only';

    /**
     * @var GetBlockEntryList
     */
    private GetBlockEntryList $getAllBlockEntries;

    /**
     * @var GetBlocksByBlockEntry
     */
    private GetBlocksByBlockEntry $getBlocksByBlockEntry;

    /**
     * @var File
     */
    private File $fileDriver;

    /**
     * @var GetBlockEntryByKey
     */
    private GetBlockEntryByKey $getBlockEntryByKey;

    /**
     * @var FileSystem
     */
    private FileSystem $fileSystem;

    /**
     * @var GetContentProvisioningFiles
     */
    private GetContentProvisioningFiles $getContentProvisioningFiles;

    /**
     * @param GetBlockEntryList $getAllBlockEntries
     * @param GetBlocksByBlockEntry $getBlocksByBlockEntry
     * @param File $fileDriver
     * @param GetBlockEntryByKey $getBlockEntryByKey
     * @param FileSystem $fileSystem
     * @param GetContentProvisioningFiles $getContentProvisioningFiles
     * @param string|null $name
     */
    public function __construct(
        GetBlockEntryList $getAllBlockEntries,
        GetBlocksByBlockEntry $getBlocksByBlockEntry,
        File $fileDriver,
        GetBlockEntryByKey $getBlockEntryByKey,
        FileSystem $fileSystem,
        GetContentProvisioningFiles $getContentProvisioningFiles,
        string $name = null
    ) {
        $this->getAllBlockEntries = $getAllBlockEntries;
        $this->getBlocksByBlockEntry = $getBlocksByBlockEntry;
        $this->fileDriver = $fileDriver;
        $this->getBlockEntryByKey = $getBlockEntryByKey;
        $this->fileSystem = $fileSystem;
        $this->getContentProvisioningFiles = $getContentProvisioningFiles;
        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption(self::PARAM_CHANGED_ONLY)) {
            $entries = $this->getChangedEntries();
        } else {
            $entries = $this->getAllBlockEntries->get();
        }

        $this->renderTable($input, $output, $entries);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('content-provisioning:block:list');
        $this->setDescription('List all configured CMS block entries');
        $this->addOption(
            self::PARAM_CHANGED_ONLY,
            null,
            InputOption::VALUE_OPTIONAL,
            'Parameter to list (only) changed blocks.',
        );
        parent::configure();
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getChangedEntries(): ?array
    {
        $files = $this->getContentProvisioningFiles->execute();
        return $this->getChangedBlocksIdentifier($this->getBlockInformation($files));
    }

    /**
     * Render table with CMS block information
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $entries
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function renderTable(InputInterface $input, OutputInterface $output, array $entries)
    {
        $table = new Table($output);
        $table->setHeaders(['Key', 'Identifier', 'Stores', 'Maintained', 'Active', 'Title', 'in DB (IDs)']);

        foreach ($entries as $entry) {
            $table->addRow(
                [
                    $entry['key'],
                    $entry['identifier'],
                    (!empty($entry['stores'])) ? (is_array($entry['stores']) ?
                        implode(', ', $entry['stores']) : $entry['stores']) : "null",
                    $entry['is_maintained'],
                    $entry['is_active'],
                    $entry['title'],
                    $this->getExistsInDbValue($entry['key']),
                ]
            );
        }

        $table->render($output);
    }

    /**
     * Transform array values into a string to be displayed in the email
     * @param array $entries
     * @return string
     */
    public function renderValuesForEmail(array $entries): string
    {
        $entriesList = [];
        foreach ($entries as $entry) {
            $entriesList[] = "key: " . $entry['key'];
            $entriesList[] = "identifier: " . $entry['identifier'];
        }
        $separatedEntries = implode(", ", $entriesList);
        return $separatedEntries;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getExistsInDbValue(string $key): string
    {
        $entry = $this->getBlockEntryByKey->get($key);
        try {
            $ids = [];
            foreach ($this->getBlocksByBlockEntry->execute($entry) as $page) {
                $ids[] = $page->getId();
            }

            if (empty($ids)) {
                return 'no';
            }

            return 'yes (' . implode(', ', $ids) . ')';
        } catch (LocalizedException $noSuchEntityException) {
            return 'ERROR: ' . $noSuchEntityException->getMessage();
        }
    }

    /**
     * Return array with changed blocks identifier
     * @param array $filesPath
     * @return array|null
     */
    public function getChangedBlocksIdentifier(array $filesPath): ?array
    {
        $changedBlocks = [];
        foreach ($filesPath as $file) {
            try {
                // Get Block content from code
                $filePath = $this->resolvePath($file['content']);
                $codeFileContent = $this->fileDriver->fileGetContents($filePath);
            } catch (LocalizedException $e) {
                return 'ERROR: ' . $e->getMessage();
            }
            // Get Block content from database
            $dbBlockContent = $this->getBlockEntryByKey->get($file['key'])->getContent();

            if ($codeFileContent !== $dbBlockContent) {
                $changedBlocks[] = $file;
            }
        }
        return $changedBlocks;
    }

    /**
     * Return array with block information from content_provisioning file
     * @param array $files
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getBlockInformation(array $files): array
    {
        $results = [];
        foreach ($files as $fileName => $fileContent) {
            try {
                $xml = new \SimpleXMLElement($fileContent);

                foreach ($xml->block as $block) {
                    $stores = (string)(isset($block->stores->store) && $block->stores->store->attributes() ?
                        $block->stores->store->attributes()->code : null);
                    $results[] = [
                        'identifier' => (string)$block['identifier'],
                        'key' => (string)$block['key'],
                        'content' => (string)$block->content,
                        'stores' => $stores,
                        'is_active' => (string)$block['active'] ? 'yes' : 'no',
                        'is_maintained' => (string)$block['maintained'] ? 'yes' : 'no',
                        'title' => $block->title
                    ];
                }
            } catch (LocalizedException $e) {
                error_log($e->getMessage());
            }
        }
        return $results;
    }

    /**
     * Transform given file path in PHP readble path
     * @param string $filePath
     * @return string
     */
    public function resolvePath(string $filePath): string
    {
        $pathInfo = $this->fileSystem->getPathInfo($filePath);
        $resolvedDirName = str_replace(['::', "_"], '/', $pathInfo['dirname']);
        $fullPath= $resolvedDirName . "/" . $pathInfo['basename'];
        $readblePath = $this->fileDriver->getRealPath("src/app/code/" . $fullPath);

        return $readblePath;
    }
}
