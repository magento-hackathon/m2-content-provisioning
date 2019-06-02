<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\Export;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Strategy\ExportToModule;
use PHPUnit\Framework\MockObject\MockObject;

class ExportPageEntryToModuleTest extends PageExportTestCase
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

        $entry = $this->pageEntryFactory->create(['data' => [
            PageEntryInterface::TITLE => 'Test Page 1',
            PageEntryInterface::CONTENT => '<h2>test foobar Aenean commodo ligula eget dolor aenean massa</h2>',
            PageEntryInterface::CONTENT_HEADING => 'Some Content Heading',
            PageEntryInterface::KEY => 'test.page.1',
            PageEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-1',
            PageEntryInterface::IS_ACTIVE => false,
            PageEntryInterface::IS_MAINTAINED => true,
            PageEntryInterface::STORES => ['default', 'admin'],
            PageEntryInterface::META_DESCRIPTION => 'Some seo description',
            PageEntryInterface::META_KEYWORDS => 'Some, seo, keywords',
            PageEntryInterface::META_TITLE => 'Seo title',
            PageEntryInterface::PAGE_LAYOUT => '3columns',
            PageEntryInterface::LAYOUT_UPDATE_XML => '<foo>bar</foo>',
            PageEntryInterface::CUSTOM_THEME => 3,
            PageEntryInterface::CUSTOM_THEME_FROM => '2019-03-29',
            PageEntryInterface::CUSTOM_THEME_TO => '2019-05-29',
            PageEntryInterface::CUSTOM_ROOT_TEMPLATE => '3columns',
        ]]);

        $this->export->execute(
            $strategyMock,
            $entry
        );

        $targetXmlPath = 'app/code/ModuleNamespace/CustomModule/etc/content_provisioning.xml';
        $expectedXmlPath = __DIR__ . '/_files/export-page-entry-to-module.xml';
        $this->assertTrue($this->fileSystem->hasChild($targetXmlPath));
        $this->assertFileExists($this->fileSystem->getChild($targetXmlPath)->url());
        $this->assertXmlStringEqualsXmlString(
            file_get_contents($expectedXmlPath),
            file_get_contents($this->fileSystem->getChild($targetXmlPath)->url())
        );
    }
}
