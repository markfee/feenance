<?php namespace Feenance\services\Currency;


class DecimalMainToSubConverter implements CurrencyConverterInterface {

    public function convert($amount)
    {
        return is_null($amount) ? $amount : (integer) ($amount * 100);
    }
}