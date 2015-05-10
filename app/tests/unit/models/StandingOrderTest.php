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

    public function test_increment_until_modifies_the_next_date_and_prev_date()
    {   // If I pass in true to the until function I expect the iterator to actually modify the standing order previous and next dates.
        $standingOrder = new StandingOrder([
            "next_date" => "2015-08-01", "finish_date" => "2015-08-31", "account_id" => 1, "amount" => 10.45
        ]);
        $count = 0;

        foreach($standingOrder->until("2015-08-07", true) as $transaction) {
            $count++;
            $this->assertTrue($transaction instanceof Transaction);
        }

        $this->assertTrue($count == 7, "Count of 7 expected {$count}");

        $this->assertTrue($standingOrder->nextDateIs("2015-08-08"), "Expected Next Date to be modified: {$standingOrder->getNextDate()}");
        $this->assertTrue($standingOrder->getPreviousDate()->isSameDay(new Carbon("2015-08-07")), "Expected Previous Date to be modified: {$standingOrder->getPreviousDate()}");
        $this->assertTrue($standingOrder->getFinishDate()->isSameDay(new Carbon("2015-08-31")), "Expected Finish Date not to be modified: {$standingOrder->getFinishDate()}");
    }

    public function test_increment_past_finish_sets_next_date_to_null()
    {   // If I pass in true to the until function I expect the iterator to actually modify the standing order previous and next dates.
        $standingOrder = new StandingOrder([
            "next_date" => "2015-08-01", "finish_date" => "2015-08-03", "account_id" => 1, "amount" => 10.45
        ]);
        $count = 0;

        foreach($standingOrder->until("2015-08-07", true) as $transaction) {
            $count++;
            $this->assertTrue($transaction instanceof Transaction);
        }

        $this->assertTrue($count == 3, "Count of 7 expected {$count}");

        $this->assertTrue($standingOrder->nextDateIs(null), "Expected Next Date to be null: {$standingOrder->getNextDate()}");
        $this->assertTrue($standingOrder->getPreviousDate()->isSameDay(new Carbon("2015-08-03")), "Expected Previous Date to be 2015-08-03: {$standingOrder->getPreviousDate()}");
        $this->assertTrue($standingOrder->getFinishDate()->isSameDay(new Carbon("2015-08-03")), "Expected Finish Date not to be 2015-08-03: {$standingOrder->getFinishDate()}");
    }


    public function test_increment_by_month()
    {   // If I pass in true to the until function I expect the iterator to actually modify the standing order previous and next dates.
        $standingOrder = new StandingOrder([
            "next_date" => "2015-08-01", "account_id" => 1, "amount" => 10.45, "increment_unit" => "m"
        ]);
        $count = 0;

        foreach($standingOrder->until("2016-08-01", true) as $transaction) {
            $count++;
            $this->assertTrue($transaction instanceof Transaction);
        }

        $this->assertTrue($count == 13, "Count of 13 expected - got: {$count}");

        $this->assertTrue($standingOrder->nextDateIs("2016-09-01"), "Expected Next Date to be null: {$standingOrder->getNextDate()}");
        $this->assertTrue($standingOrder->getPreviousDate()->isSameDay(new Carbon("2016-08-01")), "Expected Previous Date to be 2016-08-01: {$standingOrder->getPreviousDate()}");
        $this->assertTrue($standingOrder->getFinishDate() == null, "Expected Finish Date to be null: {$standingOrder->getFinishDate()}");
    }

    public function test_increment_by_month_will_honour_exceptions()
    {   // If I pass in true to the until function I expect the iterator to actually modify the standing order previous and next dates.
        $standingOrder = new StandingOrder([
            "next_date" => "2015-08-01", "account_id" => 1, "amount" => 10.45, "increment_unit" => "m", "exceptions" => "Feb||Mar"
        ]);

        $count = 0;

        foreach($standingOrder->until("2016-08-01", true) as $transaction) {
            $count++;
            $this->assertTrue($transaction instanceof Transaction);
        }

        $this->assertTrue($count == 11, "Count of 11 expected - got: {$count}");

        $this->assertTrue($standingOrder->nextDateIs("2016-09-01"), "Expected Next Date to be null: {$standingOrder->getNextDate()}");
        $this->assertTrue($standingOrder->getPreviousDate()->isSameDay(new Carbon("2016-08-01")), "Expected Previous Date to be 2016-08-01: {$standingOrder->getPreviousDate()}");
        $this->assertTrue($standingOrder->getFinishDate() == null, "Expected Finish Date to be null: {$standingOrder->getFinishDate()}");
    }

};