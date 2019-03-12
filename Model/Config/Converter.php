<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config;

use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Converter implements ConverterInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConverterInterface
     */
    private $pageNodeConverter;

    /**
     * @var ConverterInterface
     */
    private $blockNodeConverter;

    /**
     * @param ConverterInterface $pageNodeConverter
     * @param ConverterInterface $blockNodeConverter
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConverterInterface $pageNodeConverter,
        ConverterInterface $blockNodeConverter,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->pageNodeConverter = $pageNodeConverter;
        $this->blockNodeConverter = $blockNodeConverter;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function convert($source): array
    {
        try {
            return [
                'pages' => $this->pageNodeConverter->convert($source),
                'blocks' => $this->blockNodeConverter->convert($source),
            ];
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }
    }
}
