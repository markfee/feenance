<?php namespace Feenance\tests\unit\StatementImporter;

use Feenance\models\Transaction;
use Feenance\repositories\file_readers\FirstDirectCSVReader;
use Feenance\tests\TestCase;
use Feenance\Services\StatementImporter;

class FirstDirectFileReaderTest extends TestCase {

    private $file_path;

    protected function getReader() {
        $this->file_path = base_path() . "/app/tests/unit/StatementImporter/test_firstdirect.csv";
        $reader = new FirstDirectCSVReader();
        $this->AssertTrue($reader->open($this->file_path), "Failed to open {$this->file_path}");
        return $reader;

    }

    public function test_I_can_create_an_instance() {
        $reader = $this->getReader();
        $this->AssertTrue($reader->valid(), "Reader is Not Valid! {$this->file_path}");
    }

    public function test_I_can_iterate_over_the_file() {
        $count = 0;
        $reader = $this->getReader();
        print ("\n");

        foreach($reader as $key=>$record) {
            print "\n" . $record;
            $count++;
        }
        $this->assertTrue($count > 0, "Expecting more than 0 lines");
    }

    public function test_each_iteration_should_be_a_transaction() {
        $count = 0;
        $reader = $this->getReader();

        foreach($reader as $transaction) {
            $this->assertTrue(is_a($transaction, "Feenance\models\Transaction"), "Should be an instance of a transaction");
        }
    }

    public function test_each_transaction_has_a_non_zero_amount() {
        $count = 0;
        $reader = $this->getReader();

        foreach($reader as $transaction) {
            $this->AssertTrue($transaction->getAmount() != 0.0, "Amount Should be non Zero");
        }
    }




}

;