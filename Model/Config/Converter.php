<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config;

use DOMDocument;
use DOMElement;
use DOMNode;
use Firegento\ContentProvisioning\Api\StoreCodeResolverInterface;
use Firegento\ContentProvisioning\Model\ContentResolverProvider;
use Magento\Framework\Exception\LocalizedException;

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
     * @param StoreCodeResolverInterface $storeCodeResolver
     * @param ContentResolverProvider $contentResolverProvider
     */
    public function __construct(
        StoreCodeResolverInterface $storeCodeResolver,
        ContentResolverProvider $contentResolverProvider
    ) {
        $this->storeCodeResolver = $storeCodeResolver;
        $this->contentResolverProvider = $contentResolverProvider;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function convert($source): array
    {
        return [
            'pages' => $this->extractCmsEntityNodes($source, 'page'),
            'blocks' => $this->extractCmsEntityNodes($source, 'block'),
        ];
    }

    /**
     * @param DOMDocument $config
     * @return array
     * @throws LocalizedException
     */
    private function extractCmsEntityNodes(DOMDocument $config, string $nodeKey): array
    {
        $output = [];
        /** @var $node DOMElement */
        foreach ($config->getElementsByTagName($nodeKey) as $node) {
            $identifier = $this->getAttributeValue($node, 'identifier');
            $stores = $this->extractStores($node);
            $output[$this->buildKey($identifier, $stores)] = [
                'identifier' => $identifier,
                'maintained' => (bool)$this->getAttributeValue($node, 'maintained', false),
                'stores' => $stores,
                'content' => $this->extractContent($node)
            ];
        }
        return $output;
    }

    /**
     * @param string $identifier
     * @param array $stores
     * @return string
     */
    private function buildKey(string $identifier, array $stores): string
    {
        return md5($identifier . json_encode($stores));
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
