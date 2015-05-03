<?php namespace Feenance\models;

use \Carbon\Carbon;
use \JsonSerializable;

class Transaction implements JsonSerializable, BankTransactionInterface, ExtendedArrayableInterface {
    use BankStringTrait;
    use CategorisableTrait;
    use BatchTrait;
    use HasCurrencyTrait;

    /*** @var Carbon    */  private $date           = null;
    /*** @var int       */  private $amount         = null;    // in pence
    /*** @var int       */  private $balance        = null;    // Calculated balance at the time of the transaction
    /*** @var int       */  private $bank_balance   = null;    // Bank Balance at the time of the transaction
    /*** @var int       */  private $account_id     = null;
    /*** @var int       */  private $transfer_id    = null;

    /*** @var bool      */  private $reconciled     = false;
    /*** @var string    */  private $notes          = null;

    function __construct($param = null, $amount = 0, $currency_code = null)
    {
       if ( is_a($param, "\Carbon\Carbon") ) {
           $this->setCurrencyCode($currency_code);
           $this->setDate($param);
           $this->setAmount($amount);
        } elseif ( is_array($param) ) {
           $this->fromArray($param);
        }
    }

    public function toArray()
    {
        return  array_merge([
            "currency_code" =>  $this->getCurrencyCode(),
            "date" =>           $this->getDate(),
            "amount" =>         $this->getAmount(),
            "balance" =>        $this->getBalance(),
            "bank_balance" =>   $this->getBankBalance(),
            "account_id" =>     $this->getAccountId(),
            "transfer_id" =>    $this->getTransferId(),
            "reconciled" =>     $this->isReconciled(),
            "bank_string" =>    $this->getBankString(),
            "notes" =>          $this->getNotes(),

            "batch_id" =>       $this->getBatchId(),
        ],  $this->toBankStringArray(),
            $this->toCategorisableArray()
        );
    }

    public function fromArray($setValues)
    {
        // First make sure we have all of the required elements in our array
        // by creating a valid empty structure
        $param = array_merge($this->toArray(), $setValues);

        // Then call the setters.
        $this->setCurrencyCode($param["currency_code"]);
        $this->setDate($param["date"]);
        $this->setAmount($param["amount"]);
        $this->setBalance($param["balance"]);
        $this->setBankBalance($param["bank_balance"]);
        $this->setAccountId($param["account_id"]);
        $this->setTransferId($param["transfer_id"]);
        $this->setReconciled($param["reconciled"]);
        $this->setNotes($param["notes"]);

        $this->setBatchId($param["batch_id"]);

        $this->fromBankStringArray($param);
        $this->fromCategorisableArray($param);
    }

    public function toStorageArray()
    {
        $array = $this->toArray();
        $array["amount"] = $this->amount;
        $array["bank_balance"] = $this->bank_balance;
        $array["balance"] = $this->balance;
        return $array;
    }

    /**
     * Create the model from an internally supplied array
     * such as from an eloquent database query
     * @param $setValues array
     */
    public function fromStorageArray($setValues)
    {
        // TODO: Implement fromStorageArray() method.
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return json_encode($this);
    }

    /*** @return Carbon */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param Carbon $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->convertToMainCurrency($this->amount);
    }


    /**
     * @param float $amount
     * @param bool $in_pence
     */
    public function setAmount($amount)
    {
        $this->amount = $this->convertToSubCurrency($amount);
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->account_id;
    }

    /**
     * @param int $account_id
     */
    public function setAccountId($account_id)
    {
        $this->account_id = $account_id;
    }

    /**
     * @return boolean
     */
    public function isReconciled()
    {
        return $this->reconciled;
    }

    /**
     * @param boolean $reconciled
     */
    public function setReconciled($reconciled)
    {
        $this->reconciled = $reconciled;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->convertToMainCurrency($this->balance);
    }

    /**
     * @param int $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $this->convertToSubCurrency($balance);
    }

    /**
     * @return int
     */
    public function getBankBalance()
    {
        return $this->convertToMainCurrency($this->bank_balance);
    }

    /**
     * @param int $bank_balance
     */
    public function setBankBalance($bank_balance)
    {
        $this->bank_balance = $this->convertToSubCurrency($bank_balance);
    }

    public function negateAmount()
    {
        $this->amount *= -1;
    }

    /**
     * @return int
     */
    public function getTransferId()
    {
        return $this->transfer_id;
    }

    /**
     * @param int $transfer_id
     */
    public function setTransferId($transfer_id)
    {
        $this->transfer_id = $transfer_id;
    }

    /*** @return bool */
    public function isTransfer()
    {
        return !empty($this->transfer_id);
    }

    /*** @return BankTransactionInterface */
    public function getTransfer()
    {
        if ( ! $this->isTransfer() ) {
            return null;
        }
        $transfer = clone($this);
        $transfer->setAccountId($this->getTransferId());
        $transfer->negateAmount();
        return $transfer;
    }

}