<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\PageInstaller;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterfaceFactory;
use Firegento\ContentProvisioning\Model\PageInstaller;
use Firegento\ContentProvisioning\Model\Query\GetFirstPageByPageEntry;
use Firegento\ContentProvisioning\Model\Query\GetPageEntryList;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var GetPageEntryList|MockObject
     */
    protected $getPageEntryListMock;

    /**
     * @var PageInstaller
     */
    protected $installer;

    /**
     * @var PageEntryInterfaceFactory
     */
    protected $pageEntryInterfaceFactory;

    /**
     * @var PageEntryInterface[]
     */
    protected $pageEntries;

    /**
     * @var GetFirstPageByPageEntry
     */
    protected $getFisrtPageByPageEntry;


    protected function setUp(): void
    {
        parent::setUp();

        $this->getPageEntryListMock = self::getMockBuilder(GetPageEntryList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->installer = Bootstrap::getObjectManager()
            ->create(PageInstaller::class, ['getAllPageEntries' => $this->getPageEntryListMock]);

        $this->pageEntryInterfaceFactory = Bootstrap::getObjectManager()
            ->create(PageEntryInterfaceFactory::class);

        $this->storeManager = Bootstrap::getObjectManager()
            ->create(StoreManagerInterface::class);

        $this->getFisrtPageByPageEntry = Bootstrap::getObjectManager()
            ->create(GetFirstPageByPageEntry::class);
    }

    protected function tearDown(): void
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

    protected function initEntries()
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
            PageEntryInterface::LAYOUT_UPDATE_XML => '',
            PageEntryInterface::CUSTOM_THEME => 3,
            PageEntryInterface::CUSTOM_THEME_FROM => '2019-03-29',
            PageEntryInterface::CUSTOM_THEME_TO => '2019-05-29',
            PageEntryInterface::CUSTOM_ROOT_TEMPLATE => '3columns',
        ]]);

        $this->pageEntries[2] = $this->pageEntryInterfaceFactory->create(['data' => [
            PageEntryInterface::TITLE => 'Test Page 2',
            PageEntryInterface::CONTENT => file_get_contents(__DIR__ . '/../../_files/content/dummy-content.html'),
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
            PageEntryInterface::LAYOUT_UPDATE_XML => '',
        ]]);

        $this->getPageEntryListMock->method('get')->willReturn($this->pageEntries);
    }

    protected function comparePageWithEntryForStore($entryIndex)
    {
        $entry = $this->pageEntries[$entryIndex];
        $block = $this->getPageByPageEntry($entry);

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

    /**
     * @param PageEntryInterface $entry
     * @return PageInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function getPageByPageEntry(PageEntryInterface $entry): PageInterface
    {
        return $this->getFisrtPageByPageEntry->execute($entry);
    }
}
