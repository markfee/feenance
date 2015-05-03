<?php namespace Feenance\services\Currency;


class DecimalMainToSubConverter extends BaseCurrencyConverter {

    public function convert($amount)
    {
        return is_null($amount) ? $amount : (integer) ($amount * 100);
    }
}