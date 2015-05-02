<?php namespace Feenance\services\Currency;

use Mockery\CountValidator\Exception;

class CurrencyConverter implements CurrencyConverterInterface
{
    public static function create($fromCurrencyCode, $toCurrencyCode)
    {
        if (strcasecmp($fromCurrencyCode, $toCurrencyCode) === 0) {
            return new static; // default converter performs no conversion at all
        }

        if (CurrencyConverter::is_sub_unit_of($fromCurrencyCode, $toCurrencyCode)) {
            return new DecimalSubToMainConverter();
        }

        if (CurrencyConverter::is_main_unit_of($fromCurrencyCode, $toCurrencyCode)) {
            return new DecimalMainToSubConverter();
        }

        throw new Exception("Currency Converter Not Found");

    }

    public function convert($amount)
    {
        return $amount;
    }

    public static function is_sub_unit_of($fromCurrencyCode, $toCurrencyCode) {
        return (static::is_sub_unit($fromCurrencyCode) && substr_compare($fromCurrencyCode, $toCurrencyCode, 0, 3) == 0);
    }

    public static function is_main_unit_of($fromCurrencyCode, $toCurrencyCode) {
        return (static::is_main_unit($fromCurrencyCode) && substr_compare($fromCurrencyCode, $toCurrencyCode, 0, 3) == 0);
    }

    public static function is_sub_unit($currencyCode)
    {
        return strlen($currencyCode) > 3;
    }

    public static function is_main_unit($currencyCode)
    {
        return strlen($currencyCode) === 3;
    }
}

