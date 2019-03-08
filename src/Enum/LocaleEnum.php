<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Class LocaleEnum
 *
 * @package App\Enum
 */
class LocaleEnum
{
    public const LOCALE_EN = 'en';
    public const LOCALE_FR = 'fr';

    public const LOCALES = [
        self::LOCALE_EN,
        self::LOCALE_FR
    ];

    public const LOCALES_TRANS_KEYS = [
        self::LOCALE_EN => 'english',
        self::LOCALE_FR => 'french'
    ];

    /**
     * @return array
     */
    public static function getLocaleLabels() : array
    {
        return self::LOCALES_TRANS_KEYS;
    }
}
