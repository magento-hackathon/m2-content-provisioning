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

namespace Firegento\ContentProvisioning\Model\Query;

use Magento\Framework\Module\ModuleList;
use Magento\Framework\Module\Dir;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Exception\LocalizedException;

class GetContentProvisioningFiles
{
    /**
     * @var ModuleList
     */
    private ModuleList $moduleList;

    /**
     * @var Dir
     */
    private Dir $moduleDirReader;

    /**
     * @var File
     */
    private File $fileDriver;

    /**
     * @param ModuleList $moduleList
     * @param Dir $moduleDirReader
     * @param File $fileDriver
     */
    public function __construct(
        ModuleList $moduleList,
        Dir $moduleDirReader,
        File $fileDriver
    ) {
        $this->moduleList = $moduleList;
        $this->moduleDirReader = $moduleDirReader;
        $this->fileDriver = $fileDriver;
    }

    /**
     * @return array|string
     * @throws \LogicException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        $files = [];
        $modules = $this->moduleList->getAll();

        foreach ($modules as $moduleName => $moduleData) {
            try {
                $modulePath = $this->moduleDirReader->getDir($moduleName);

                // only app/code modules
                if (!str_contains($modulePath, 'app/code')) {
                    continue;
                }

                $moduleEtcPath = $this->moduleDirReader->getDir($moduleName, Dir::MODULE_ETC_DIR);
                $provisionFilePath = $moduleEtcPath . DIRECTORY_SEPARATOR . 'content_provisioning.xml';

                if ($this->fileDriver->isExists($provisionFilePath)) {
                    $content = $this->fileDriver->fileGetContents($provisionFilePath);
                    $files[$moduleName] = $content;
                }
            } catch (LocalizedException $e) {
                return 'ERROR: ' . $e->getMessage();
            }
        }
        return $files;
    }
}
