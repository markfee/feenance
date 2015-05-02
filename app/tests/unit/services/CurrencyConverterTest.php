<?php namespace Feenance\tests\unit\models;

use Feenance\tests\TestCase;
use Feenance\services\Currency\CurrencyConverter;


class CurrencyConverterTest extends TestCase {

    public function test_I_can_create_an_instance() {
        $currency_converter = new CurrencyConverter;
        $this->assertTrue($currency_converter instanceof CurrencyConverter);
    }

    public function test_sub_unit_of() {
        $currency_converter  = new CurrencyConverter;
        $this->assertTrue($currency_converter->is_sub_unit_of("GBP_pence", "GBP"));
        $this->assertFalse($currency_converter->is_sub_unit_of("GBP", "GBP_pence"));
        $this->assertFalse($currency_converter->is_sub_unit_of("USD_cent", "GBP"));
    }

    public function test_main_unit_of() {
        $currency_converter  = new CurrencyConverter;
        $this->assertTrue($currency_converter->is_main_unit_of("GBP", "GBP_pence"));
        $this->assertFalse($currency_converter->is_main_unit_of("GBP_pence", "GBP"));
        $this->assertFalse($currency_converter->is_main_unit_of("USD", "GBP_pence"));
    }


};