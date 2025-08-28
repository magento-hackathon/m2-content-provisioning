<?php
declare(strict_types=1);

/**
 * Copyright (c) 2025 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see http://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 */

namespace Firegento\ContentProvisioning\Model\Config;

use Magento\Config\Model\Config\CommentInterface;

/**
 * @copyright  Copyright (c) 2025 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 *
 * @link       https://www.techdivision.com/
 * @author     Team Zero <zero@techdivision.com>
 */
class CronExpressionComment implements CommentInterface
{
    /**
     * @param mixed $elementValue
     * @return string
     */
    public function getCommentText(mixed $elementValue): string
    {
        $cronGuruUrl = $this->getCronGuruUrl($elementValue);
        $timezoneUrl = $this->getTimeZoneUrl();
        $crontabLinkHtml = '<a target="_blank" href="' . $cronGuruUrl . '">crontab.guru</a>';
        $timezoneLinkHtml = '<a target="_blank" href="' . $timezoneUrl . '">UTC</a>';

        return $timezoneLinkHtml . ', see ' . $crontabLinkHtml;
    }

    /**
     * @param string|null $elementValue
     * @return string
     */
    private function getCronGuruUrl(?string $elementValue): string
    {
        $valueConverted = str_replace(' ', '_', $elementValue);
        $cronGuruUrl = 'https://crontab.guru/#' . $valueConverted;

        return $cronGuruUrl;
    }

    /**
     * @return string
     */
    private function getTimeZoneUrl(): string
    {
        $timezoneUrl = 'https://www.timeanddate.com/worldclock/converter.html';
        $timeZoneParams = [
        'p1' => 'tz_gmt', // GMT/UTC
        'p2' => 137,      // LA
        'p3' => 179,      // NY
        'p4' => 37,       // Berlin
        'p5' => 237,      // Shanghai
        'p6' => 240,      // Sydney
        ];

        $timezoneUrl .= '?' . http_build_query($timeZoneParams);

        return $timezoneUrl;
    }
}
