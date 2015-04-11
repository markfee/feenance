<?php namespace Feenance\tests\unit\StatementImporter;

use Feenance\tests\TestCase;

abstract class FileReaderTest extends TestCase {

    abstract public function getReader();
    public function getExpectedFields()
    {
        return ["bank_string_id", "amount"];
    }

    public function test_I_can_create_an_instance()
    {
        $reader = $this->getReader();
        $this->AssertTrue($reader->valid(), "Reader is Not Valid!");
    }

    public function test_I_can_iterate_over_the_file()
    {
        $count = 0;
        $reader = $this->getReader();

        foreach ($reader as $key => $record) {
            $count++;
        }
        $this->assertTrue($count > 0, "Expecting more than 0 lines");
    }

    public function test_each_iteration_should_be_a_transaction()
    {
        $reader = $this->getReader();

        foreach ($reader as $transaction) {
            $this->assertTrue(is_a($transaction, "Feenance\models\Transaction"), "Should be an instance of a transaction");
        }
    }

    public function test_each_transaction_has_a_non_zero_amount()
    {
        $count = 0;
        $reader = $this->getReader();

        foreach ($reader as $transaction) {
            $this->AssertTrue($transaction->getAmount() != 0.0, "Amount Should be non Zero");
        }
    }
}