<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterfaceFactory;
use Firegento\ContentProvisioning\Model\PageInstaller;
use Firegento\ContentProvisioning\Model\Query\GetPageEntryList;
use Magento\Cms\Api\GetPageByIdentifierInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\TestFramework\Helper\Bootstrap;

class PageInstallerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var GetPageEntryList|MockObject
     */
    private $getPageEntryListMock;

    /**
     * @var PageInstaller
     */
    private $installer;

    /**
     * @var PageEntryInterfaceFactory
     */
    private $pageEntryInterfaceFactory;

    /**
     * @var GetPageByIdentifierInterface
     */
    private $getPageByIdentifier;

    /**
     * @var PageEntryInterface[]
     */
    private $pageEntries;


    protected function setUp()
    {
        parent::setUp();

        $this->getPageEntryListMock = self::getMockBuilder(GetPageEntryList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->installer = Bootstrap::getObjectManager()
            ->create(PageInstaller::class, ['getAllPageEntries' => $this->getPageEntryListMock]);

        $this->pageEntryInterfaceFactory = Bootstrap::getObjectManager()
            ->create(PageEntryInterfaceFactory::class);

        $this->getPageByIdentifier = Bootstrap::getObjectManager()
            ->create(GetPageByIdentifierInterface::class);

        $this->storeManager = Bootstrap::getObjectManager()
            ->create(StoreManagerInterface::class);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $connection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $connection->getConnection()->delete(
            $connection->getTableName('cms_page'),
            [
                'identifier LIKE ?' => 'firegento-content-provisioning-test-%',
            ]
        );
        $connection->getConnection()->delete(
            $connection->getTableName('url_rewrite'),
            [
                'request_path LIKE ?' => 'firegento-content-provisioning-test-%',
                'entity_type = ?' => 'cms-page',
            ]
        );
    }

    private function initEntries()
    {
        $this->pageEntries[1] = $this->pageEntryInterfaceFactory->create(['data' => [
            PageEntryInterface::TITLE => 'Test Page 1',
            PageEntryInterface::CONTENT => '<h2>test foobar Aenean commodo ligula eget dolor aenean massa</h2>',
            PageEntryInterface::CONTENT_HEADING => 'Some Content Heading',
            PageEntryInterface::KEY => 'test.page.1',
            PageEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-1',
            PageEntryInterface::IS_ACTIVE => false,
            PageEntryInterface::IS_MAINTAINED => true,
            PageEntryInterface::STORES => ['admin'],
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

        $this->pageEntries[2] = $this->pageEntryInterfaceFactory->create(['data' => [
            PageEntryInterface::TITLE => 'Test Page 2',
            PageEntryInterface::CONTENT => file_get_contents(__DIR__ . '/_files/dummy-content.html'),
            PageEntryInterface::CONTENT_HEADING => 'Some Content Heading',
            PageEntryInterface::KEY => 'test.page.2',
            PageEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-2',
            PageEntryInterface::IS_ACTIVE => true,
            PageEntryInterface::IS_MAINTAINED => false,
            PageEntryInterface::STORES => ['default', 'admin'],
        ]]);

        $this->pageEntries[3] = $this->pageEntryInterfaceFactory->create(['data' => [
            PageEntryInterface::TITLE => 'Test Page 3',
            PageEntryInterface::CONTENT => '<h2>test foobar Aenean commodo ligula eget dolor aenean massa</h2>',
            PageEntryInterface::CONTENT_HEADING => 'Some Content Heading',
            PageEntryInterface::KEY => 'test.page.3',
            PageEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-3',
            PageEntryInterface::IS_ACTIVE => true,
            PageEntryInterface::IS_MAINTAINED => true,
            PageEntryInterface::STORES => ['default'],
            PageEntryInterface::META_DESCRIPTION => 'Some seo description',
            PageEntryInterface::META_KEYWORDS => 'Some, seo, keywords',
            PageEntryInterface::META_TITLE => 'Seo title',
            PageEntryInterface::PAGE_LAYOUT => '3columns',
            PageEntryInterface::LAYOUT_UPDATE_XML => '<foo>bar</foo>'
        ]]);

        $this->getPageEntryListMock->method('get')->willReturn($this->pageEntries);
    }

    private function comparePageWithEntryForStore($entryIndex, $storeCode)
    {
        $entry = $this->pageEntries[$entryIndex];
        $block = $this->getPageByIdentifier->execute(
            $entry->getIdentifier(),
            (int)$this->storeManager->getStore($storeCode)->getId()
        );

        $this->assertSame($block->getTitle(), $entry->getTitle());
        $this->assertSame($block->getContent(), $entry->getContent());
        $this->assertSame($block->isActive(), $entry->isActive());
        $this->assertSame($block->getMetaDescription(), $entry->getMetaDescription());
        $this->assertSame($block->getMetaKeywords(), $entry->getMetaKeywords());
        $this->assertSame($block->getMetaTitle(), $entry->getMetaTitle());
        $this->assertSame($block->getCustomLayoutUpdateXml(), $entry->getCustomLayoutUpdateXml());
        $this->assertSame($block->getPageLayout(), $entry->getPageLayout());
        $this->assertSame($block->getContentHeading(), $entry->getContentHeading());
    }

    public function testInstall()
    {
        $this->initEntries();

        $this->installer->install();

        // Verify, that pages are in database like defined
        $this->comparePageWithEntryForStore(1, 'admin');
        $this->comparePageWithEntryForStore(2, 'default');
        $this->comparePageWithEntryForStore(2, 'admin');
        $this->comparePageWithEntryForStore(3, 'default');
    }

    public function testInstallUpdateMaintainedPages()
    {
        $this->initEntries();

        $this->installer->install();

        // Change page entry values
        $this->pageEntries[1]->setTitle('Changed Page 1');
        $this->pageEntries[1]->setIsActive(true);
        $this->pageEntries[1]->setContent('New Content');

        $this->pageEntries[2]->setTitle('Changed Page 2');
        $this->pageEntries[2]->setIsActive(true);
        $this->pageEntries[2]->setContent('New Content');

        $this->pageEntries[3]->setTitle('Changed Page 3');
        $this->pageEntries[3]->setContent('New Content 333');
        $this->pageEntries[3]->setMetaDescription('New Meta description');
        $this->pageEntries[3]->setPageLayout('1column');
        $this->pageEntries[3]->setContentHeading('Another Content Heading');

        // Execute installer a second time
        $this->installer->install();

        // Verify that first and third page was updated
        $this->comparePageWithEntryForStore(1, 'admin');
        $this->comparePageWithEntryForStore(3, 'default');

        // Verify that second page did not change
        $entry = $this->pageEntries[2];
        $block = $this->getPageByIdentifier->execute(
            $entry->getIdentifier(),
            (int)$this->storeManager->getStore('default')->getId()
        );

        $this->assertNotSame($block->getTitle(), $entry->getTitle());
        $this->assertNotSame($block->getContent(), $entry->getContent());
    }
}
