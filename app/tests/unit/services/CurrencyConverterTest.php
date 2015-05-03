<?php namespace Feenance\tests\unit\models;

use Feenance\services\Currency\DecimalMainToSubConverter;
use Feenance\services\Currency\DecimalSubToMainConverter;
use Feenance\services\Currency\NullCurrencyConverter;
use Feenance\tests\TestCase;
use Feenance\services\Currency\Currency;


class CurrencyConverterTest extends TestCase {

    public function test_I_can_create_valid_instances() {
        $this->assertTrue(Currency::createConverter("", "")             instanceof NullCurrencyConverter);
        $this->assertTrue(Currency::createConverter("GBP", "GBP")       instanceof NullCurrencyConverter);
        $this->assertTrue(Currency::createConverter("GBP", "GBP_pence") instanceof DecimalMainToSubConverter);
        $this->assertTrue(Currency::createConverter("GBP_pence", "GBP") instanceof DecimalSubToMainConverter);
        $this->assertTrue(Currency::createMainConverter("GBP") instanceof NullCurrencyConverter);
        $this->assertTrue(Currency::createMainConverter("GBP_pence") instanceof DecimalSubToMainConverter);
        $this->assertTrue(Currency::createMainConverter(null) instanceof DecimalSubToMainConverter);
        $this->assertTrue(Currency::createSubConverter("GBP_pence") instanceof NullCurrencyConverter);
        $this->assertTrue(Currency::createSubConverter("GBP") instanceof DecimalMainToSubConverter);
        $this->assertTrue(Currency::createSubConverter(null) instanceof DecimalMainToSubConverter);
    }

    public function test_sub_unit_of() {
        $this->assertTrue(Currency::is_sub_unit_of("GBP_pence", "GBP"));
        $this->assertFalse(Currency::is_sub_unit_of("GBP", "GBP_pence"));
        $this->assertFalse(Currency::is_sub_unit_of("USD_cent", "GBP"));
    }

    public function test_main_unit_of() {
        $this->assertTrue(Currency::is_main_unit_of("GBP", "GBP_pence"));
        $this->assertFalse(Currency::is_main_unit_of("GBP_pence", "GBP"));
        $this->assertFalse(Currency::is_main_unit_of("USD", "GBP_pence"));
    }

    public function test_get_sub_unit() {
        $this->assertTrue(Currency::get_sub_unit("GBP") === "GBP_pence");
        $this->assertTrue(Currency::get_sub_unit("GBP_pence") === "GBP_pence");
    }

    public function test_get_main_unit() {
        $this->assertTrue(Currency::get_main_unit("GBP") === "GBP");
        $this->assertTrue(Currency::get_main_unit("GBP_pence") === "GBP");
    }

};