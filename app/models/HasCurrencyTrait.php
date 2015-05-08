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
        $this->currencyConverter = $this->currencyConverter ?: Currency::createSubConverter($this->getCurrencyCode());
        return $this->currencyConverter;
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
        $this->currency_code = $this->currency_code  ?: Currency::defaultMainCurrencyCode();
        return $this->currency_code;
    }

    /**
     * @param string $currency_code
     */
    public function setCurrencyCode($currency_code = null)
    {
        try {
            if (!is_string($this->currency_code) || !is_string($currency_code) || strcmp($currency_code, $this->currency_code) != 0) {
                $old_code = $this->currency_code;
                $this->currencyConverter = null;
                $this->currency_code = $currency_code ?: Currency::defaultMainCurrencyCode();
                return $old_code;
            }
            return $this->currency_code;
        } catch(Exception $ex) {
            dd("abort");
            dd($ex);
        }
    }
}