<?php namespace Feenance\services\Currency;

class NullCurrencyConverter extends BaseCurrencyConverter {

    public function convert($amount)
    {
        return $amount;
    }

    public function convertBack($amount)
    {
        return $amount;
    }


}