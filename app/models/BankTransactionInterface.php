<?php namespace Feenance\models;

use Carbon\Carbon;
use Feenance\models\CategorisableInterface;

interface BankTransactionInterface extends BatchInterface, CategorisableInterface {
    /**
     * @return string
     */
    public function getBankString();

    /**
     * @param string $bank_string
     */
    public function setBankString($bank_string);

    /**
     * @return Carbon
     */
    public function getDate();

    /**
     * @param Carbon $date
     */
    public function setDate($date);

    /**
     * @return int
     */
    public function getAmount();
    public function negateAmount();

    /**
     * @param int $amount
     * @param bool $transformed
     */
    public function setAmount($amount, $transformed = false);

    /**
     * @return int
     */
    public function getAccountId();

    /**
     * @param int $account_id
     */
    public function setAccountId($account_id);

    /**
     * @return boolean
     */
    public function isReconciled();

    /**
     * @param boolean $reconciled
     */
    public function setReconciled($reconciled);

    /**
     * @return int
     */
    public function getBalance();

    /**
     * @param int $balance
     * @param bool $transformed
     */
    public function setBalance($balance, $transformed = false);

    /**
     * @return int
     */
    public function getBankBalance();

    /**
     * @param int $balance
     * @param bool $transformed
     */
    public function setBankBalance($balance, $transformed = false);


    // Transferable
    /*** @return bool*/                     public function isTransfer();
    /*** @return int*/                      public function getTransferId();
    /*** @param int $transfer_id*/          public function setTransferId($transfer_id);
    /*** @return BankTransactionInterface*/ public function getTransfer();


}