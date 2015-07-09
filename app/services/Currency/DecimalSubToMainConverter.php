<?php namespace Feenance\services\Currency;


class DecimalSubToMainConverter extends BaseCurrencyConverter {

    public function convert($amount)
    {
        return is_null($amount) ? $amount : (float) ($amount * 0.01);
    }

    public function convertBack($amount)
    {
        return is_null($amount) ? $amount : (int) round($amount * 100, 0);
    }
}