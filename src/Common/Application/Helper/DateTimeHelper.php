<?php

namespace Common\Application\Helper;

use DateTime;

class DateTimeHelper
{
    const POSSIBLE_FORMATS = [
        'Y-m-d',
        'Y-m-d H',
        'Y-m-d H:i',
        'Y-m-d H:i:s',
    ];

    /**
     * @param mixed $dateTime
     *
     * @return DateTime|false
     */
    public static function buildDateTimeObject($dateTime)
    {
        if (!\is_string($dateTime)) {
            return false;
        }

        foreach (self::POSSIBLE_FORMATS as $format) {
            //exclamation mark is used to set unsent DateTime parts to 0
            $d = DateTime::createFromFormat(sprintf('!%s', $format), $dateTime);
            if ($d && $d->format($format) === $dateTime) {
                return $d;
            }
        }

        return false;
    }
}
