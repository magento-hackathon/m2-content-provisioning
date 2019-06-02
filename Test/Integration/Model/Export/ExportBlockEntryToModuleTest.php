<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\Export;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Model\Strategy\ExportToModule;
use PHPUnit\Framework\MockObject\MockObject;

class ExportBlockEntryToModuleTest extends BlockExportTestCase
{
    public function testExecute()
    {
        /** @var ExportToModule|MockObject $strategyMock */
        $strategyMock = $this->getMockBuilder(ExportToModule::class)
            ->disableOriginalConstructor()
            ->getMock();

        $strategyMock->method('getXmlPath')->will($this->returnValue(
            $this->fileSystem->getChild('app/code/ModuleNamespace/CustomModule')->url() .
            '/etc/content_provisioning.xml'
        ));
        $strategyMock->method('getContentDirectoryPath')->will($this->returnValue(
            $this->fileSystem->getChild('app/code/ModuleNamespace/CustomModule')->url() .
            '/content'
        ));
        $strategyMock->method('getMediaDirectoryPath')->will($this->returnValue(
            $this->fileSystem->getChild('app/code/ModuleNamespace/CustomModule')->url() .
            '/content/media'
        ));
        $strategyMock->method('getContentNamespacePath')->will($this->returnValue(
            'ModuleNamespace_CustomModule::content'
        ));
        $strategyMock->method('getMediaNamespacePath')->will($this->returnValue(
            'ModuleNamespace_CustomModule::content/media'
        ));

        $entry = $this->blockEntryFactory->create(['data' => [
            BlockEntryInterface::TITLE => 'Test Export Block 1',
            BlockEntryInterface::CONTENT => '<h2>test foobar Aenean commodo ligula eget dolor aenean massa</h2>',
            BlockEntryInterface::KEY => 'test.export.block.1',
            BlockEntryInterface::IDENTIFIER => 'firegento-content-provisioning-export-test-1',
            BlockEntryInterface::IS_ACTIVE => false,
            BlockEntryInterface::IS_MAINTAINED => true,
            BlockEntryInterface::STORES => ['admin'],
        ]]);

        $this->export->execute(
            $strategyMock,
            $entry
        );

        $targetXmlPath = 'app/code/ModuleNamespace/CustomModule/etc/content_provisioning.xml';
        $expectedXmlPath = __DIR__ . '/_files/export-block-entry-to-module.xml';
        $this->assertTrue($this->fileSystem->hasChild($targetXmlPath));
        $this->assertFileExists($this->fileSystem->getChild($targetXmlPath)->url());
        $this->assertXmlStringEqualsXmlString(
            file_get_contents($expectedXmlPath),
            file_get_contents($this->fileSystem->getChild($targetXmlPath)->url())
        );
    }
}
