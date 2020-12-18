<?php

namespace Common\Application\Helper;

class RegexHelper
{
    const POSITIVE_DECIMAL_NUMBER = '^\d+([\.\,]\d+)?$';
    const DECIMAL_NUMBER = '^[-+]?[0-9]*[.,]?[0-9]+$';
    const OCLOCK = '\d{1,2}:\d{1,2}';
    const DATE = '^\d\d?\.\d\d?\.\d\d\d\d\.$';
    const DATE_TIME = '^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$';
    const ONLY_DECIMAL_NUMBER = '[^0-9.,]';

    public static function getDecimalNumberExpression(): string
    {
        return sprintf('/%s/', self::DECIMAL_NUMBER);
    }

    public static function getPositiveDecimalNumberExpression(): string
    {
        return sprintf('/%s/', self::POSITIVE_DECIMAL_NUMBER);
    }

    public static function getDateExpression(): string
    {
        return sprintf('/%s/', self::DATE);
    }

    public static function getOClockExpression(): string
    {
        return sprintf('/%s/', self::OCLOCK);
    }

    public static function getOnlyDecimalNumberExpression(): string
    {
        return sprintf('/%s/', self::ONLY_DECIMAL_NUMBER);
    }

    public static function getDateTimeExpression(): string
    {
        return sprintf('/%s/', self::DATE_TIME);
    }
}
