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
    /*** @var integer   */  private $bank_string_id = null;

    /*** @return array*/
    public function toBankStringArray()
    {
        return [
            "bank_string" =>        $this->getBankString(),
            "bank_string_id" =>     $this->getBankStringId(),
        ];
    }

    /*** @param array*/
    public function fromBankStringArray($param)
    {
        $this->setBankString($param["bank_string"]);
        $this->setBankStringId($param["bank_string_id"]);
    }


    /**
     * @return int
     */
    public function getBankStringId()
    {
        return $this->bank_string_id;
    }

    /**
     * @param int $bank_string_id
     */
    public function setBankStringId($bank_string_id)
    {
        $this->bank_string_id = $bank_string_id;
    }

    /**
     * @return bool
     */
    public function hasBankStringId() {
        return !empty($this->bank_string_id);
    }

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

    /**
     * @return bool
     */
    public function hasBankString() {
        return !empty($this->bank_string);
    }


}