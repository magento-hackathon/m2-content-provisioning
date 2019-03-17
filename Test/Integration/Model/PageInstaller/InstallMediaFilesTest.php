<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\PageInstaller;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Api\TargetMediaDirectoryPathProviderInterface;
use Firegento\ContentProvisioning\Model\Command\ApplyMediaFiles;
use Magento\TestFramework\Helper\Bootstrap;
use Firegento\ContentProvisioning\Model\PageInstaller;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;

class InstallMediaFilesTest extends TestCase
{
    /**
     * @var TargetMediaDirectoryPathProviderInterface|MockObject
     */
    protected $targetMediaDirectoryPathProvider;

    /**
     * @var vfsStreamDirectory
     */
    protected $fileSystem;

    protected function setUp()
    {
        parent::setUp();

        $this->targetMediaDirectoryPathProvider = self::getMockBuilder(TargetMediaDirectoryPathProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $structure = [
            'source' => [
                'media' => [
                    'file-1.png' => 'some value',
                    'file-2.txt' => 'some value',
                    'not-used.png' => 'some value',
                    'sub-directory' => [
                        'file-3.jpg' => 'some value'
                    ],
                    'existing' => [
                        'file-4.gif' => 'some value'
                    ]
                ]
            ],
            'pub' => [
                'media' => [
                    'existing' => []
                ]
            ],
        ];

        $this->fileSystem = vfsStream::setup('root', null, $structure);

        $this->targetMediaDirectoryPathProvider->method('get')->willReturn(
            $this->fileSystem->getChild('pub/media')->url()
        );

        $applyMediaFiles = Bootstrap::getObjectManager()
            ->create(ApplyMediaFiles::class, [
                'targetMediaDirectoryPathProvider' => $this->targetMediaDirectoryPathProvider,
            ]);

        $this->installer = Bootstrap::getObjectManager()
            ->create(PageInstaller::class, [
                'getAllPageEntries' => $this->getPageEntryListMock,
                'applyMediaFiles' => $applyMediaFiles
            ]);
    }

    protected function initEntries()
    {
        $this->pageEntries[1] = $this->pageEntryInterfaceFactory->create(['data' => [
            PageEntryInterface::TITLE => 'Page with images 1',
            PageEntryInterface::CONTENT => '',
            PageEntryInterface::CONTENT_HEADING => 'Some Content Heading',
            PageEntryInterface::KEY => 'page.with.images.1',
            PageEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-images-1',
            PageEntryInterface::IS_ACTIVE => true,
            PageEntryInterface::IS_MAINTAINED => true,
            PageEntryInterface::STORES => ['admin'],
            PageEntryInterface::MEDIA_DIRECTORY => $this->fileSystem->getChild('source/media')->url(),
            PageEntryInterface::MEDIA_FILES => [
                'sub-directory/file-3.jpg',
                'file-1.png',
            ]
        ]]);

        $this->pageEntries[2] = $this->pageEntryInterfaceFactory->create(['data' => [
            PageEntryInterface::TITLE => 'Page with images 2',
            PageEntryInterface::CONTENT => '',
            PageEntryInterface::CONTENT_HEADING => '',
            PageEntryInterface::KEY => 'page.with.images.2',
            PageEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-images-2',
            PageEntryInterface::IS_ACTIVE => true,
            PageEntryInterface::IS_MAINTAINED => true,
            PageEntryInterface::STORES => ['admin'],
            PageEntryInterface::MEDIA_DIRECTORY => $this->fileSystem->getChild('source/media')->url(),
            PageEntryInterface::MEDIA_FILES => [
                'file-2.txt',
                'existing/file-4.gif',
            ]
        ]]);

        $this->pageEntries[3] = $this->pageEntryInterfaceFactory->create(['data' => [
            PageEntryInterface::TITLE => 'Page with images 3',
            PageEntryInterface::CONTENT => '',
            PageEntryInterface::CONTENT_HEADING => '',
            PageEntryInterface::KEY => 'page.with.images.3',
            PageEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-images-3',
            PageEntryInterface::IS_ACTIVE => true,
            PageEntryInterface::IS_MAINTAINED => true,
            PageEntryInterface::STORES => ['admin'],
            PageEntryInterface::MEDIA_DIRECTORY => $this->fileSystem->getChild('source/media')->url(),
            PageEntryInterface::MEDIA_FILES => [
                'sub-directory/file-3.jpg',
                'file-1.png',
                'not-existing/image.png',
            ]
        ]]);

        $this->getPageEntryListMock->method('get')->willReturn($this->pageEntries);
    }

    public function testInstall()
    {
        $this->initEntries();

        $this->installer->install();

        $this->assertTrue($this->fileSystem->hasChild('pub/media/file-1.png'));
        $this->assertTrue($this->fileSystem->hasChild('pub/media/file-2.txt'));
        $this->assertTrue($this->fileSystem->hasChild('pub/media/sub-directory/file-3.jpg'));
        $this->assertTrue($this->fileSystem->hasChild('pub/media/existing/file-4.gif'));

        $this->assertFalse($this->fileSystem->hasChild('pub/media/not-used.png'));

        $this->assertFalse($this->fileSystem->hasChild('pub/media/not-existing'));
        $this->assertFalse($this->fileSystem->hasChild('pub/media/not-existing/image.png'));
    }
}
