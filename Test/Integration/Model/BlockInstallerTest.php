<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterfaceFactory;
use Firegento\ContentProvisioning\Model\BlockInstaller;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryList;
use Firegento\ContentProvisioning\Model\Query\GetFirstBlockByBlockEntry;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\TestFramework\Helper\Bootstrap;

class BlockInstallerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BlockInstaller
     */
    private $installer;

    /**
     * @var GetBlockEntryList|MockObject
     */
    private $getBlockEntryListMock;

    /**
     * @var BlockEntryInterfaceFactory
     */
    private $blockEntryInterfaceFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var BlockEntryInterface[]
     */
    private $blockEntries = [];

    /**
     * @var GetFirstBlockByBlockEntry
     */
    private $getFirstBlockByBlockEntry;

    protected function setUp()
    {
        parent::setUp();

        $this->getBlockEntryListMock = self::getMockBuilder(GetBlockEntryList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->installer = Bootstrap::getObjectManager()
            ->create(BlockInstaller::class, ['getAllBlockEntries' => $this->getBlockEntryListMock]);

        $this->blockEntryInterfaceFactory = Bootstrap::getObjectManager()
            ->create(BlockEntryInterfaceFactory::class);

        $this->storeManager = Bootstrap::getObjectManager()
            ->create(StoreManagerInterface::class);

        $this->getFirstBlockByBlockEntry = Bootstrap::getObjectManager()
            ->create(GetFirstBlockByBlockEntry::class);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $connection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $connection->getConnection()->delete(
            $connection->getTableName('cms_block'),
            [
                'identifier LIKE ?' => 'firegento-content-provisioning-test-%',
            ]
        );
    }

    private function initBlockEntries()
    {
        $this->blockEntries[1] = $this->blockEntryInterfaceFactory->create(['data' => [
            BlockEntryInterface::TITLE => 'Test Block 1',
            BlockEntryInterface::CONTENT => '<h2>test foobar Aenean commodo ligula eget dolor aenean massa</h2>',
            BlockEntryInterface::KEY => 'test.block.1',
            BlockEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-1',
            BlockEntryInterface::IS_ACTIVE => false,
            BlockEntryInterface::IS_MAINTAINED => true,
            BlockEntryInterface::STORES => ['admin'],
        ]]);

        $this->blockEntries[2] = $this->blockEntryInterfaceFactory->create(['data' => [
            BlockEntryInterface::TITLE => 'Test Block 2',
            BlockEntryInterface::CONTENT => file_get_contents(__DIR__ . '/_files/dummy-content.html'),
            BlockEntryInterface::KEY => 'test.block.2',
            BlockEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-2',
            BlockEntryInterface::IS_ACTIVE => true,
            BlockEntryInterface::IS_MAINTAINED => false,
            BlockEntryInterface::STORES => ['default', 'admin'],
        ]]);

        $this->getBlockEntryListMock->method('get')->willReturn($this->blockEntries);
    }

    private function compareBlockWithEntryForStore($entryIndex)
    {
        $entry = $this->blockEntries[$entryIndex];
        $block = $this->getBlockByBlockEntry($entry);

        $this->assertSame($block->getTitle(), $entry->getTitle());
        $this->assertSame($block->getContent(), $entry->getContent());
        $this->assertSame($block->isActive(), $entry->isActive());
    }

    /**
     * @param BlockEntryInterface $entry
     * @return BlockInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getBlockByBlockEntry(BlockEntryInterface $entry): BlockInterface
    {
        return $this->getFirstBlockByBlockEntry->execute($entry);
    }

    public function testInstall()
    {
        $this->initBlockEntries();

        $this->installer->install();

        // Verify, that block are in database like defined
        $this->compareBlockWithEntryForStore(1);
        $this->compareBlockWithEntryForStore(2);
    }

    public function testInstallUpdateMaintainedBlocks()
    {
        $this->initBlockEntries();

        $this->installer->install();

        // Change block entry values
        $this->blockEntries[1]->setTitle('Changed Block 1');
        $this->blockEntries[1]->setIsActive(true);
        $this->blockEntries[1]->setContent('New Content');

        $this->blockEntries[2]->setTitle('Changed Block 2');
        $this->blockEntries[2]->setIsActive(true);
        $this->blockEntries[2]->setContent('New Content');

        // Execute installer a second time
        $this->installer->install();

        // Verify that first block was updated
        $this->compareBlockWithEntryForStore(1);

        // Verify that second block did not change
        $entry = $this->blockEntries[2];
        $block = $this->getBlockByBlockEntry($entry);

        $this->assertNotSame($block->getTitle(), $entry->getTitle());
        $this->assertNotSame($block->getContent(), $entry->getContent());
    }
}
