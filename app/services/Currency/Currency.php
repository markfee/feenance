<?php namespace Feenance\services\Currency;

use Mockery\CountValidator\Exception;

abstract class Currency implements CurrencyConverterInterface
{
    public static function defaultMainCurrencyCode() {
        return "GBP";
    }

    public static function defaultSubCurrencyCode() {
        return "GBP_pence";
    }

    public static function equal($a, $b)
    {
        return abs($a-$b) < 0.00001;
    }

    public static function createConverter($fromCurrencyCode, $toCurrencyCode)
    {
        if (strcasecmp($fromCurrencyCode, $toCurrencyCode) === 0) {
            return new NullCurrencyConverter(); // default converter performs no conversion at all
        }

        if (Currency::is_sub_unit_of($fromCurrencyCode, $toCurrencyCode)) {
            return new DecimalSubToMainConverter();
        }

        if (Currency::is_main_unit_of($fromCurrencyCode, $toCurrencyCode)) {
            return new DecimalMainToSubConverter();
        }

        throw new Exception("Currency Converter Not Found");
    }

    public static function createSubConverter($currencyCode) {
        $currencyCode = $currencyCode ?: Currency::defaultMainCurrencyCode();
        return Currency::createConverter($currencyCode, Currency::get_sub_unit($currencyCode));
    }

    public static function createMainConverter($currencyCode) {
        $currencyCode = $currencyCode ?: Currency::defaultSubCurrencyCode();
        return Currency::createConverter($currencyCode, Currency::get_main_unit($currencyCode));
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

    public static function get_sub_unit($currencyCode)
    {
        $currencyCode = $currencyCode ?: Currency::defaultSubCurrencyCode();
        return static::is_sub_unit($currencyCode) ? $currencyCode : $currencyCode . "_pence";
    }

    public static function get_main_unit($currencyCode)
    {
        $currencyCode = $currencyCode ?: Currency::defaultMainCurrencyCode();
        return substr($currencyCode, 0, 3);
    }

}