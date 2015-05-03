<?php namespace Feenance\services\Currency;

interface CurrencyConverterInterface
{
    /**
     * @param $amount
     * @return mixed
     */
    public function convert($amount);

    /**
     * @param $amount
     * @return mixed
     */
    public function convertBack($amount);

    /**
     * @param $array
     * @param $key
     * @return mixed
     */
    public function convert_array_item($array, $key);
}