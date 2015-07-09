<?php namespace Feenance\services\Currency;


class DecimalMainToSubConverter extends BaseCurrencyConverter {

    public function convert($amount)
    {
        return is_null($amount) ? $amount : (int) round($amount * 100, 0);
    }

    public function convertBack($amount)
    {
        return is_null($amount) ? $amount : (float) ($amount * 0.01);
    }

}