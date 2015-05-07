<?php namespace Feenance\tests\unit\models;

use Feenance\models\Transaction;
use Feenance\tests\TestCase;
use Feenance\models\StandingOrder;


class StandingOrderTest extends TestCase {

    public function test_I_can_create_a_standing_order() {
        $standingOrder = new StandingOrder(["name" => "My Standing Order"]);
        $this->assertTrue($standingOrder->getName() === "My Standing Order");
        $this->assertFalse($standingOrder->isValid());
    }

    public function test_I_can_create_a_set_and_get_same_amount() {
        $standingOrder = new StandingOrder();
        $standingOrder->setAmount(10.45);
    }

    public function test_I_can_create_a_one_off_standing_order() {
        $standingOrder = new StandingOrder([
            "next_date" => "2015-08-01", "account_id" => 1, "amount" => 10.45
        ]);

        $this->assertTrue($standingOrder->isValid());

        $this->assertTrue(dd($standingOrder->getAmount()));

        $transaction = $standingOrder->getNextTransaction();

        $this->assertTrue($transaction instanceOf Transaction);
        $this->assertTrue($transaction->getAmount() == 10.45, "Amount should be 10.45 {$transaction->getAmount()}");
    }


};