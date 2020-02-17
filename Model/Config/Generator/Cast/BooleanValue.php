<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator\Cast;

class BooleanValue
{
    /**
     * @param $value
     * @return string
     */
    public function execute($value): string
    {
        if ($value) {
            return 'true';
        }

        return 'false';
    }
}