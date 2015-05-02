<?php namespace Feenance\services\Currency;


class DecimalSubToMainConverter implements CurrencyConverterInterface {

    public function convert($amount)
    {
        return is_null($amount) ? $amount : (float) ($amount * 0.01);
    }
}