<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use SimpleXMLElement;

interface GeneratorInterface
{
    public function execute(EntryInterface $entry, SimpleXMLElement $xml): void;
}