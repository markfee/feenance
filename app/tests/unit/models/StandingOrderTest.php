<?php namespace Feenance\tests\unit\models;

use Carbon\Carbon;
use Feenance\models\Transaction;
use Feenance\tests\TestCase;
use Feenance\models\StandingOrder;
use Feenance\services\Currency\Currency;
use Markfee\MyCarbon;
use Feenance\models\InfiniteStandingOrderIteratorException;

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

        $amount = $standingOrder->getAmount();
        $this->assertTrue(Currency::equal($standingOrder->getAmount(), 10.45), "Standing Order amount should be 10.45 {$standingOrder->getAmount()}");

        $transaction = $standingOrder->getNextTransaction();

        $this->assertTrue($transaction instanceOf Transaction);
        $this->assertTrue(Currency::equal($transaction->getAmount(), 10.45), "Transaction amount should be 10.45 {$transaction->getAmount()}");
        $this->assertTrue($transaction->isValid(), "Transaction should be valid");
        $this->assertTrue($transaction->getDate()->diffInDays(new Carbon("2015-08-01")) == 0, "Transaction date should be 01/08/2015 {$transaction->getDate()}");
    }

    public function test_an_attempt_to_iterate_without_a_finish_will_throw_an_exception()
    {
        $standingOrder = new StandingOrder([
            "next_date" => "2015-08-01", "account_id" => 1, "amount" => 10.45
        ]);
        try {
            foreach ($standingOrder as $transaction) {
            }
        } catch (InfiniteStandingOrderIteratorException $ex) {
            return true;
        }
        $this->assertTrue(false, "An InfiniteStandingOrderIteratorException was expected");
    }

    public function test_I_can_iterate_a_one_off_standing_order()
    {
        $standingOrder = new StandingOrder([
            "next_date" => "2015-08-01", "finish_date" => "2015-08-01", "account_id" => 1, "amount" => 10.45
        ]);
        $count = 0;

        foreach($standingOrder as $transaction) {
            $count++;
            $this->assertTrue($transaction instanceof Transaction);
        }

        $this->assertTrue($count == 1, "Count of 1 expected {$count}");

        $this->assertTrue($standingOrder->nextDateIs("2015-08-01"), "Expected Next Date not to be modified: {$standingOrder->getNextDate()}");

    }

    public function test_I_can_iterate_a_standing_order_for_a_week()
    {
        $standingOrder = new StandingOrder([
            "next_date" => "2015-08-01", "account_id" => 1, "amount" => 10.45
        ]);
        $count = 0;

        foreach($standingOrder->until("2015-08-07") as $transaction) {
            $count++;
            $this->assertTrue($transaction instanceof Transaction);
        }

        $this->assertTrue($count == 7, "Count of {$count} expected 7");
    }

    public function test_I_can_iterate_until_a_different_cuttoff()
    {
        $standingOrder = new StandingOrder([
            "next_date" => "2015-08-01", "finish_date" => "2015-08-31", "account_id" => 1, "amount" => 10.45
        ]);
        $count = 0;

        foreach($standingOrder->until("2015-08-10") as $transaction) {
            $count++;
            $this->assertTrue($transaction instanceof Transaction);
        }

        $this->assertTrue($count == 10, "Count of 10 expected {$count}");

        $this->assertTrue($standingOrder->nextDateIs("2015-08-01"), "Expected Next Date not to be modified: {$standingOrder->getNextDate()}");
        $this->assertTrue($standingOrder->getFinishDate()->isSameDay(new Carbon("2015-08-31")), "Expected Finish Date not to be modified: {$standingOrder->getNextDate()}");

    }

    public function test_iterate_until_a_later_cuttoff_will_still_stop_at_finish_date()
    {
        $standingOrder = new StandingOrder([
            "next_date" => "2015-08-01", "finish_date" => "2015-08-31", "account_id" => 1, "amount" => 10.45
        ]);
        $count = 0;

        foreach($standingOrder->until("2015-09-10") as $transaction) {
            $count++;
            $this->assertTrue($transaction instanceof Transaction);
        }

        $this->assertTrue($count == 31, "Count of 31 expected {$count}");

        $this->assertTrue($standingOrder->nextDateIs("2015-08-01"), "Expected Next Date not to be modified: {$standingOrder->getNextDate()}");
        $this->assertTrue($standingOrder->getFinishDate()->isSameDay(new Carbon("2015-08-31")), "Expected Finish Date not to be modified: {$standingOrder->getNextDate()}");

    }





};