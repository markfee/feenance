<?php namespace Feenance\models;

use Carbon\Carbon;
use Feenance\models\CategorisableInterface;

interface BankTransactionInterface extends BatchInterface, CategorisableInterface {

    /**
     * @return CurrencyConverterInterface
     */
    public function getCurrencyConverter();
    /**
     * @param CurrencyConverterInterface $currencyConverter
     * @return CurrencyConverterInterface $old_currencyConverter
     */
    public function setCurrencyConverter($currencyConverter);

    /**
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @param string $currency_code
     * @return string $old_currency_code
     */
    public function setCurrencyCode($currency_code);

    public function convertToSubCurrency($amount);
    public function convertToMainCurrency($amount);

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
    public function setAmount($amount);

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
     */
    public function setBalance($balance);

    /**
     * @return int
     */
    public function getBankBalance();

    /**
     * @param int $balance
     */
    public function setBankBalance($balance);


    // Transferable
    /*** @return bool*/                     public function isTransfer();
    /*** @return int*/                      public function getTransferId();
    /*** @param int $transfer_id*/          public function setTransferId($transfer_id);
    /*** @return BankTransactionInterface*/ public function getTransfer();


}