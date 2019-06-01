<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\Export;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterfaceFactory;
use Firegento\ContentProvisioning\Api\ExportInterface;
use Firegento\ContentProvisioning\Api\StrategyInterface;
use Firegento\ContentProvisioning\Model\Strategy\ExportToModule;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;

class BlockExportTestCase extends TestCase
{
    /** @var ExportInterface */
    protected $export;

    /** @var BlockEntryInterfaceFactory */
    protected $blockEntryFactory;

    /** @var StrategyInterface|MockObject */
    protected $strategyMock;

    /** @var vfsStreamDirectory */
    protected $fileSystem;

    protected function setUp()
    {
        parent::setUp();

        /** @var ExportInterface $export */
        $this->export = Bootstrap::getObjectManager()
            ->create(ExportInterface::class);

        /** @var BlockEntryInterfaceFactory $blockEntryFactory */
        $this->blockEntryFactory = Bootstrap::getObjectManager()
            ->create(BlockEntryInterfaceFactory::class);

        /** @var StrategyInterface|MockObject $strategyMock */
        $this->strategyMock = self::getMockBuilder(ExportToModule::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fileSystem = vfsStream::setup('root', null, [
            'app' => [
                'code' => [
                    'ModuleNamespace' => [
                        'CustomModule' => [
                            'etc' => [
                                'module.xml' => 'XML-Content...'
                            ],
                            'registration.php' => 'PHP-Content...',
                            'composer.json' => 'JSON-Content',
                        ]
                    ]
                ]
            ],
            'pub' => [
                'media' => [
                    'existing' => [
                        'dummy-image.png' => '',
                    ],
                    'image.jpg' => '',
                ]
            ],
        ]);

        $this->strategyMock->method('buildTargetPath')->will($this->returnValue(
            $this->fileSystem->getChild('app/code/ModuleNamespace/CustomModule')->url()
        ));
    }
}
