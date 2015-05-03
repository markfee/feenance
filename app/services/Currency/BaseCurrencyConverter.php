<?php namespace Feenance\services\Currency;


abstract class BaseCurrencyConverter implements CurrencyConverterInterface {

    public function convert_array_item($array, $key)
    {
        return (isset($array[$key])) ? $this->convert($array[$key]) : null;
    }
}