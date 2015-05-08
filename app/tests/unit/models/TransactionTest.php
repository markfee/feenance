<?php namespace Feenance\tests\unit\models;

use Feenance\models\Transaction;
use Feenance\tests\TestCase;
use Feenance\services\Currency\Currency;
use Carbon\Carbon;

class TransactionTest extends TestCase {
    private         $newTransaction = [
        "date" => null,
        "amount" => 10.45,
        "account_id" => 1,
        "reconciled" => true,
        "payee_id" => null,
        "category_id" => null,
        "notes" => "this is a test",
        "bank_balance" => 100.23
    ];

    function __construct()
    {
        $this->newTransaction["date"] = Carbon::now();
    }

    public function test_I_can_create_a_standing_order() {
        /** @var $transaction Transaction */
        $transaction = new Transaction(["bank_string" => "My Standing Order"]);
        $this->assertTrue($transaction->getBankString() === "My Standing Order", "{$transaction->getBankString()}");
    }

    public function test_I_can_create_a_set_and_get_same_amount() {
        $transaction = new Transaction();
        $transaction->setAmount(10.45);
        $this->assertTrue(Currency::equal($transaction->getAmount(), 10.45), "Transaction amount should be 10.45 {$transaction->getAmount()}");
    }

    public function test_I_can_create_a_transaction() {
        $transaction = new Transaction($this->newTransaction);

        $this->assertTrue($transaction->isValid(), "Is Transaction valid");

        $amount = $transaction->getAmount();
        $this->assertTrue(Currency::equal($transaction->getAmount(), 10.45), "Transaction amount should be 10.45 {$transaction->getAmount()}");
        $this->assertTrue(Currency::equal($transaction->getBankBalance(), 100.23), "Transaction amount should be 100.23 {$transaction->getAmount()}");
    }

    public function test_I_can_get_a_storage_array()
    {
        $transaction = new Transaction($this->newTransaction);
        $array = $transaction->toStorageArray();
        $this->assertTrue(Currency::equal($array['amount'], 1045), "Transaction amount should be 1045 {$array['amount']}");
    }


};