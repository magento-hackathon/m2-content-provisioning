<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\Config\Parser\Query;

use Firegento\ContentProvisioning\Api\MediaFilesParserInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchMediaFilesChain;
use PHPUnit\Framework\MockObject\MockObject;

class FetchMediaFilesChainTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FetchMediaFilesChain
     */
    private $chain;

    /**
     * @var MediaFilesParserInterface|MockObject
     */
    private $parser1;

    /**
     * @var MediaFilesParserInterface|MockObject
     */
    private $parser2;

    protected function setUp()
    {
        $this->parser1 = self::getMockBuilder(MediaFilesParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser2 = self::getMockBuilder(MediaFilesParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->chain = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(FetchMediaFilesChain::class, ['parsers' => [
                $this->parser1,
                $this->parser2,
            ]]);
    }

    public function testMergingData()
    {
        $this->parser1->method('execute')->willReturn([
            'path/to/file1.png',
            'path/to/file2.png',
            'file3.png',
        ]);
        $this->parser2->method('execute')->willReturn([
            'file3.png',
            'some/other/path.jpg',
        ]);

        $result = $this->chain->execute('');

        $this->assertSame([
            'path/to/file1.png',
            'path/to/file2.png',
            'file3.png',
            'file3.png',
            'some/other/path.jpg',
        ], $result);
    }
}
