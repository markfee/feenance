<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 26/03/15
 * Time: 07:19
 */

namespace Feenance\models;
use \Carbon\Carbon;
use \JsonSerializable;

class Transaction implements JsonSerializable {
    /*** @var Carbon    */  private $date;
    /*** @var int       */  private $amount         = 0; // in pence
    /*** @var int       */  private $balance        = 0; // Bank Balance at the time of the transaction
    /*** @var int       */  private $account_id     = null;
    /*** @var bool      */  private $reconciled     = false;
    /*** @var string    */  private $notes          = null;
    /*** @var string    */  private $bank_string    = null; // The line from the bank statement

    function __construct($date, $amount)
    {
        $this->date     = $date;
        $this->amount   = $amount;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize() {
        return [
            "date" =>           $this->date,
            "amount" =>         $this->amount,
            "balance" =>        $this->balance,
            "account_id" =>     $this->account_id,
            "reconciled" =>     $this->reconciled,
            "bank_string" =>    $this->bank_string,
            "notes" =>          $this->notes,
        ];
    }
    public function __toArray()
    {
        return (Array)$this;
    }

    public function __toString()
    {
        return json_encode($this);
    }

    /**
     * @return Carbon
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * @param Carbon $date
     */
    public function setDate($date) {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getAccountId() {
        return $this->account_id;
    }

    /**
     * @param int $account_id
     */
    public function setAccountId($account_id) {
        $this->account_id = $account_id;
    }

    /**
     * @return boolean
     */
    public function isReconciled() {
        return $this->reconciled;
    }

    /**
     * @param boolean $reconciled
     */
    public function setReconciled($reconciled) {
        $this->reconciled = $reconciled;
    }

    /**
     * @return string
     */
    public function getNotes() {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes) {
        $this->notes = $notes;
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
     * @return int
     */
    public function getBalance() {
        return $this->balance;
    }

    /**
     * @param int $balance
     */
    public function setBalance($balance) {
        $this->balance = $balance;
    }


}