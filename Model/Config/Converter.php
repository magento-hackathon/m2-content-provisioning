<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config;

use Firegento\ContentProvisioning\Model\Config\Converter\BlockNodesParser;
use Firegento\ContentProvisioning\Model\Config\Converter\PageNodesParser;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PageNodesParser
     */
    private $pageNodesParser;

    /**
     * @var BlockNodesParser
     */
    private $blockNodesParser;

    /**
     * @param PageNodesParser $pageNodesParser
     * @param BlockNodesParser $blockNodesParser
     * @param LoggerInterface $logger
     */
    public function __construct(
        PageNodesParser $pageNodesParser,
        BlockNodesParser $blockNodesParser,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->pageNodesParser = $pageNodesParser;
        $this->blockNodesParser = $blockNodesParser;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function convert($source): array
    {
        try {
            return [
                'pages' => $this->pageNodesParser->execute($source),
                'blocks' => $this->blockNodesParser->execute($source),
            ];
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }
    }
}
