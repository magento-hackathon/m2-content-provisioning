<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\Export;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterfaceFactory;
use Firegento\ContentProvisioning\Api\ExportInterface;
use Firegento\ContentProvisioning\Api\StrategyInterface;
use Firegento\ContentProvisioning\Model\Strategy\ExportToModule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;

class ExportBlockEntryToModule extends TestCase
{
    /**
     *
     */
    public function testExecute()
    {
        /** @var ExportInterface $export */
        $export = Bootstrap::getObjectManager()
            ->create(ExportInterface::class);

        /** @var BlockEntryInterfaceFactory $blockEntryFactory */
        $blockEntryFactory = Bootstrap::getObjectManager()
            ->create(BlockEntryInterfaceFactory::class);

        $entry = $blockEntryFactory->create(['data' => [
            BlockEntryInterface::TITLE => 'Test Block 1',
            BlockEntryInterface::CONTENT => '<h2>test foobar Aenean commodo ligula eget dolor aenean massa</h2>',
            BlockEntryInterface::KEY => 'test.block.1',
            BlockEntryInterface::IDENTIFIER => 'firegento-content-provisioning-test-1',
            BlockEntryInterface::IS_ACTIVE => false,
            BlockEntryInterface::IS_MAINTAINED => true,
            BlockEntryInterface::STORES => ['admin'],
        ]]);

        /** @var StrategyInterface|MockObject $strategyMock */
        $strategyMock = self::getMockBuilder(ExportToModule::class)
            ->disableOriginalConstructor()
            ->getMock();

        $strategyMock->method('');

        $export->execute($strategyMock, $entry);
    }
}
