<?php namespace Feenance\tests\unit\StatementImporter;

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
//        print ("\n");

        foreach($reader as $key=>$record) {
//            print_r($record);
            $count++;
        }
        $this->assertTrue($count > 0, "Expecting more than 0 lines");
    }

}

;