<?php namespace Feenance\services\Currency;


class DecimalSubToMainConverter extends BaseCurrencyConverter {

    public function convert($amount)
    {
        return is_null($amount) ? $amount : (float) ($amount * 0.01);
    }
}