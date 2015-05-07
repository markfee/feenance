<?php namespace Feenance\models;

use Feenance\services\Currency\CurrencyConverterInterface;
use Feenance\services\Currency\Currency;
use Feenance\services\Currency\NullCurrencyConverter;

trait HasCurrencyTrait {
    /*** @var string    */                      private $currency_code  = null;
    /*** @var CurrencyConverterInterface    */  private $currencyConverter  = null;

    /**
     * @return CurrencyConverterInterface
     */
    public function getCurrencyConverter()
    {
        return $this->currencyConverter ? : Currency::defaultMain();
    }

    /**
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function setCurrencyConverter($currencyConverter)
    {
        $old = $this->getCurrencyConverter();
        $this->currencyConverter = $currencyConverter ?: Currency::defaultMain();;
        return $old;
    }

    public function convertToSubCurrency($amount)
    {
        return $this->getCurrencyConverter()->convert($amount);
    }

    public function convertToMainCurrency($amount)
    {
        return $this->getCurrencyConverter()->convertBack($amount);
    }

    /**
     * getCurrencyCode()
     * @return string
     */
    public function getCurrencyCode()
    {
        $this->currencyConverter = $this->currencyConverter ?: new NullCurrencyConverter();
        return $this->currency_code  ?: Currency::defaultMain();
    }

    /**
     * @param string $currency_code
     */
    public function setCurrencyCode($currency_code)
    {
        $old_code = $this->currency_code;
        $this->currency_code = $currency_code ?: Currency::defaultMain();
        $this->currencyConverter = Currency::createSubConverter($this->currency_code);
        return $old_code;
    }
}