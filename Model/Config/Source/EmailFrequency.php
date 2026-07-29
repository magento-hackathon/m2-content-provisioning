<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see https://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 *
 * @copyright  Copyright (c) 2024 TechDivision GmbH (https://www.techdivision.com)
 * @author     TechDivision Team Zero <zero@techdivision.com>
 * @link       https://www.techdivision.com/
 */

namespace Firegento\ContentProvisioning\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class EmailFrequency implements OptionSourceInterface
{
    public const DAILY = 'daily';
    public const WEEKLY = 'weekly';
    public const MONTHLY = 'monthly';
    public const QUARTERLY = 'quarterly';
    public const YEARLY = 'yearly';

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::DAILY, 'label' => __('Daily')],
            ['value' => self::WEEKLY, 'label' => __('Weekly')],
            ['value' => self::MONTHLY, 'label' => __('Monthly')],
            ['value' => self::QUARTERLY, 'label' => __('Quarterly')],
            ['value' => self::YEARLY, 'label' => __('Yearly')]
        ];
    }
}
