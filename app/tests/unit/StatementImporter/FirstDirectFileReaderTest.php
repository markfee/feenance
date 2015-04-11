<?php namespace Feenance\tests\unit\StatementImporter;

use Feenance\repositories\file_readers\BaseFileReader;
use Feenance\repositories\file_readers\FirstDirectCSVReader;
use Feenance\tests\unit\StatementImporter\FileReaderTest;
use Feenance\tests\TestCase;

class FirstDirectFileReaderTest extends FileReaderTest {

    public function getReader()
    {
        $file_path = base_path() . "/app/tests/unit/StatementImporter/test_firstdirect.csv";
        $reader = BaseFileReader::getReaderForFile($file_path);
        $this->assertTrue(($reader instanceof FirstDirectCSVReader), "Expects an instance of FirstDirectCSVReader");
        return $reader;
    }

    public function getExpectedFields()
    {
        return ["bank_string_id", "bank_balance"];
    }
};