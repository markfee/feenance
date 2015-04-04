<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 03/04/15
 * Time: 10:16
 */

namespace Feenance\models;


trait BankStringTrait {
    /*** @var string    */  private $bank_string    = null; // The line from the bank statement

    /**
     * @return string
     */
    public function getBankString() {
        return $this->bank_string;
    }

    /**
     * @param string $bank_string
     */
    public function setBankString($bank_string) {
        $this->bank_string = $bank_string;
    }

}