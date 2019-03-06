<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterfaceFactory;
use Firegento\ContentProvisioning\Model\BlockInstaller;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryList;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\GetBlockByIdentifierInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\TestFramework\Helper\Bootstrap;

class BlockInstallerTest extends \PHPUnit\Framework\TestCase
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
     * @var GetBlockByIdentifierInterface
     */
    protected $getBlockByIdentifier;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var BlockEntryInterface[]
     */
    private $blockEntries = [];

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

        $this->getBlockByIdentifier = Bootstrap::getObjectManager()
            ->create(GetBlockByIdentifierInterface::class);

        $this->storeManager = Bootstrap::getObjectManager()
            ->create(StoreManagerInterface::class);

        $this->blockRepository = Bootstrap::getObjectManager()
            ->create(BlockRepositoryInterface::class);
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

    private function compareBlockWithEntryForStore($entryIndex, $storeCode)
    {
        $entry = $this->blockEntries[$entryIndex];
        $block = $this->getBlockByIdentifier->execute(
            $entry->getIdentifier(),
            (int)$this->storeManager->getStore($storeCode)->getId()
        );

        $this->assertSame($block->getTitle(), $entry->getTitle());
        $this->assertSame($block->getContent(), $entry->getContent());
        $this->assertSame($block->isActive(), $entry->isActive());
    }

    public function testInstall()
    {
        $this->initBlockEntries();

        $this->installer->install();

        // Verify, that block are in database like defined
        $this->compareBlockWithEntryForStore(1, 'admin');
        $this->compareBlockWithEntryForStore(2, 'default');
        $this->compareBlockWithEntryForStore(2, 'admin');
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
        $this->compareBlockWithEntryForStore(1, 'admin');

        // Verify that second block did not change
        $entry = $this->blockEntries[2];
        $block = $this->getBlockByIdentifier->execute(
            $entry->getIdentifier(),
            (int)$this->storeManager->getStore('default')->getId()
        );

        $this->assertNotSame($block->getTitle(), $entry->getTitle());
        $this->assertNotSame($block->getContent(), $entry->getContent());
    }
}
