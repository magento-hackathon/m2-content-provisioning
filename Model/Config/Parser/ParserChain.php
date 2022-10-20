<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser;

use DOMElement;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @api
 */
class ParserChain implements ConfigParserInterface
{
    /**
     * @var ConfigParserInterface[]
     */
    private $parser;

    /**
     * @param array $parser
     * @throws LocalizedException
     */
    public function __construct(array $parser)
    {
        foreach ($parser as $parserInstance) {
            if (!($parserInstance instanceof ConfigParserInterface)) {
                throw new LocalizedException(
                    __(
                        'Parser needs to be instance of %interface',
                        ['interface' => ConfigParserInterface::class]
                    )
                );
            }
        }

        $this->parser = $parser;
    }

    /**
     * @param DOMElement $element
     * @return array
     */
    public function execute(DOMElement $element): array
    {
        $data = [];

        foreach ($this->parser as $parser) {
            $data[] = $parser->execute($element);
        }

        return array_merge(...$data);
    }
}
