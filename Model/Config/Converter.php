<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config;

use DOMDocument;
use DOMElement;
use DOMNode;
use Firegento\ContentProvisioning\Api\Data\ContentEntryInterface;
use Firegento\ContentProvisioning\Api\StoreCodeResolverInterface;
use Firegento\ContentProvisioning\Model\Resolver\ContentResolverProvider;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * @var StoreCodeResolverInterface
     */
    private $storeCodeResolver;

    /**
     * @var ContentResolverProvider
     */
    private $contentResolverProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param StoreCodeResolverInterface $storeCodeResolver
     * @param ContentResolverProvider $contentResolverProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreCodeResolverInterface $storeCodeResolver,
        ContentResolverProvider $contentResolverProvider,
        LoggerInterface $logger
    ) {
        $this->storeCodeResolver = $storeCodeResolver;
        $this->contentResolverProvider = $contentResolverProvider;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function convert($source): array
    {
        return array_merge(
            $this->extractCmsEntityNodes($source, 'page'),
            $this->extractCmsEntityNodes($source, 'block')
        );
    }

    /**
     * @param DOMDocument $config
     * @return array
     */
    private function extractCmsEntityNodes(DOMDocument $config, string $nodeKey): array
    {
        $output = [];
        /** @var $node DOMElement */
        foreach ($config->getElementsByTagName($nodeKey) as $node) {
            $identifier = $this->getAttributeValue($node, 'identifier');
            try {
                $stores = $this->extractStores($node);
                $key = $this->buildKey($identifier, $stores, $nodeKey);
                $output[$key] = [
                    ContentEntryInterface::KEY => $key,
                    ContentEntryInterface::IDENTIFIER => $identifier,
                    ContentEntryInterface::TYPE => $nodeKey,
                    ContentEntryInterface::IS_MAINTAINED =>
                        (bool)$this->getAttributeValue($node, 'maintained', false),
                    ContentEntryInterface::STORES => $stores,
                    ContentEntryInterface::CONTENT => $this->extractContent($node),
                ];
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getMessage(), $exception->getTrace());
                continue;
            }
        }
        return $output;
    }

    /**
     * @param string $identifier
     * @param array $stores
     * @param string $nodeKey
     * @return string
     */
    private function buildKey(string $identifier, array $stores, string $nodeKey): string
    {
        return md5($identifier . $nodeKey . json_encode($stores));
    }

    /**
     * @param DOMElement $config
     * @return array
     */
    private function extractStores(DOMElement $config): array
    {
        $output = [];
        foreach ($config->getElementsByTagName('store') as $store) {
            $storeCodes = $this->storeCodeResolver->execute(
                (string)$this->getAttributeValue($store, 'code', '*')
            );
            $output = array_merge($output, $storeCodes);
        }

        if (empty($output)) {
            $output = $this->storeCodeResolver->execute('*');
        }

        return $output;
    }

    /**
     * @param DOMNode $node
     * @param string $attributeName
     * @param mixed $default
     * @return mixed
     */
    private function getAttributeValue(DOMNode $node, $attributeName, $default = null)
    {
        $item = $node->attributes->getNamedItem($attributeName);
        return $item ? $item->nodeValue : $default;
    }

    /**
     * @param DOMElement $config
     * @return string
     * @throws LocalizedException
     */
    private function extractContent(DOMElement $config): string
    {
        $contentNodes = $config->getElementsByTagName('content');
        $contentNode = $contentNodes[0];
        $type = $this->getAttributeValue($contentNode, 'type', 'plain');
        $contentResolver = $this->contentResolverProvider->get($type);
        return $contentResolver->execute($contentNode);
    }
}
