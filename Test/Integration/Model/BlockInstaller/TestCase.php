<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\BlockInstaller;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterfaceFactory;
use Firegento\ContentProvisioning\Model\BlockInstaller;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryList;
use Firegento\ContentProvisioning\Model\Query\GetFirstBlockByBlockEntry;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BlockInstaller
     */
    protected $installer;

    /**
     * @var GetBlockEntryList|MockObject
     */
    protected $getBlockEntryListMock;

    /**
     * @var BlockEntryInterfaceFactory
     */
    protected $blockEntryInterfaceFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var BlockEntryInterface[]
     */
    protected $blockEntries = [];

    /**
     * @var GetFirstBlockByBlockEntry
     */
    protected $getFirstBlockByBlockEntry;

    protected function setUp(): void
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

    protected function tearDown(): void
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

    protected function initBlockEntries()
    {
        $this->blockEntries[1] = $this->blockEntryInterfaceFactory->create(
            [
                'data' => [
                    BlockEntryInterface::TITLE => 'Test Block 1',
                    BlockEntryInterface::CONTENT => '<h2>test foobar Aenean commodo ligula eget '
                        . 'dolor aenean massa</h2>',
                    BlockEntryInterface::KEY => 'test.block.1',
                    BlockEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-1',
                    BlockEntryInterface::IS_ACTIVE => false,
                    BlockEntryInterface::IS_MAINTAINED => true,
                    BlockEntryInterface::STORES => ['admin'],
                ]
            ]
        );

        $this->blockEntries[2] = $this->blockEntryInterfaceFactory->create(
            [
                'data' => [
                    BlockEntryInterface::TITLE => 'Test Block 2',
                    BlockEntryInterface::CONTENT => file_get_contents(
                        __DIR__ . '/../../_files/content/dummy-content.html'
                    ),
                    BlockEntryInterface::KEY => 'test.block.2',
                    BlockEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-2',
                    BlockEntryInterface::IS_ACTIVE => true,
                    BlockEntryInterface::IS_MAINTAINED => false,
                    BlockEntryInterface::STORES => ['default', 'admin'],
                ]
            ]
        );

        $this->getBlockEntryListMock->method('get')->willReturn($this->blockEntries);
    }

    protected function compareBlockWithEntryForStore($entryIndex)
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
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function getBlockByBlockEntry(BlockEntryInterface $entry): BlockInterface
    {
        return $this->getFirstBlockByBlockEntry->execute($entry);
    }
}
