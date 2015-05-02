<?php namespace Feenance\services\Currency;

interface CurrencyConverterInterface
{
    public function convert($amount);
}