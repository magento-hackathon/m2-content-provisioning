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
        $this->assertSame(
            file_get_contents($expectedXmlPath),
            file_get_contents($this->fileSystem->getChild($targetXmlPath)->url())
        );

        $targetContentPath = 'app/code/ModuleNamespace/CustomModule/content/'
            . 'all-stores/firegento-content-provisioning-export-test-1.html';
        $this->assertTrue($this->fileSystem->hasChild($targetContentPath));
        $this->assertSame(
            $entry->getContent(),
            file_get_contents($this->fileSystem->getChild($targetContentPath)->url())
        );
    }
}
