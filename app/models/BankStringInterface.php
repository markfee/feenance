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
    /*** @return bool*/                 public function hasBankString();

    /*** @return integer*/                  public function getBankStringId();
    /*** @param integer $bank_string_id */  public function setBankStringId($bank_string_id);
    /*** @return bool*/                     public function hasBankStringId();

    /*** @return array*/               public function toBankStringArray();
    /*** @param array*/                public function fromBankStringArray($array);

}