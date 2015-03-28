<?php namespace Feenance\tests\unit\StatementImporter;

use Feenance\models\Transaction;
use Feenance\repositories\file_readers\FirstDirectCSVReader;
use Feenance\tests\TestCase;

class FirstDirectFileReaderTest extends TestCase {

    public function getReader()
    {
        $file_path = base_path() . "/app/tests/unit/StatementImporter/test_firstdirect.csv";
        $reader = new FirstDirectCSVReader();
        $reader->open($file_path);
        return $reader;

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

        foreach($reader as $key=>$record) {
            $count++;
        }
        $this->assertTrue($count > 0, "Expecting more than 0 lines");
    }

    public function test_each_iteration_should_be_a_transaction()
    {
        $reader = $this->getReader();

        foreach($reader as $transaction) {
            $this->assertTrue(is_a($transaction, "Feenance\models\Transaction"), "Should be an instance of a transaction");
        }
    }

    public function test_each_transaction_has_a_non_zero_amount()
    {
        $count = 0;
        $reader = $this->getReader();

        foreach($reader as $transaction) {
            $this->AssertTrue($transaction->getAmount() != 0.0, "Amount Should be non Zero");
        }
    }
};