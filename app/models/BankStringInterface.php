<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 03/04/15
 * Time: 10:16
 */

namespace Feenance\models;


interface BankStringInterface {
    /*** @return string*/               public function getBankString();
    /*** @param string $bank_string */  public function setBankString($bank_string);
}