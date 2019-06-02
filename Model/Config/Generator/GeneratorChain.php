<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Exception\LocalizedException;
use SimpleXMLElement;

class GeneratorChain implements GeneratorInterface
{
    /**
     * @var string
     */
    private $entryType;
    /**
     * @var GeneratorInterface[]
     */
    private $generatorList;

    /**
     * GeneratorChain constructor.
     * @param string               $entryType
     * @param GeneratorInterface[] $generatorList
     *
     * @throws LocalizedException
     */
    public function __construct(string $entryType, array $generatorList = [])
    {
        foreach ($generatorList as $generator) {
            if (!($generator instanceof GeneratorInterface)) {
                throw new LocalizedException(__(
                    'Parser needs to be instance of %interface',
                    ['interface' => GeneratorInterface::class]
                ));
            }
        }

        $this->entryType     = $entryType;
        $this->generatorList = $generatorList;
    }

    /**
     * @param EntryInterface|PageInterface|BlockInterface $entry
     * @param SimpleXMLElement                            $xml
     */
    public function execute(EntryInterface $entry, SimpleXMLElement $xml): void
    {
        foreach ($this->generatorList as $generator) {
            $generator->execute($entry, $xml);
        }
    }
}