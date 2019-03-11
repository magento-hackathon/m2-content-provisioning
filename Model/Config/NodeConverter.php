<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config;

use DOMDocument;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchAttributeValue;
use Magento\Framework\Config\ConverterInterface;

class NodeConverter implements ConverterInterface
{
    /**
     * @var ConfigParserInterface
     */
    private $configParser;

    /**
     * @var FetchAttributeValue
     */
    private $fetchAttributeValue;

    /**
     * @var string
     */
    private $nodeName;

    /**
     * @param ConfigParserInterface $configParser
     * @param FetchAttributeValue $fetchAttributeValue
     * @param string $nodeName
     */
    public function __construct(
        ConfigParserInterface $configParser,
        FetchAttributeValue $fetchAttributeValue,
        string $nodeName = ''
    ) {
        $this->configParser = $configParser;
        $this->fetchAttributeValue = $fetchAttributeValue;
        $this->nodeName = $nodeName;
    }

    /**
     * Convert config
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = [];
        foreach ($source->getElementsByTagName($this->nodeName) as $node) {
            $key = $this->fetchAttributeValue->execute($node, 'key');
            $output[$key] = $this->configParser->execute($node);
        }
        return $output;
    }
}
