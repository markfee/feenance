<?php namespace Feenance\services\Currency;

interface CurrencyConverterInterface
{
    public function convert($amount);
    public function convert_array_item($array, $key);
}