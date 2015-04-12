<?php namespace Feenance\tests\unit\StatementImporter;

use Feenance\repositories\file_readers\BaseFileReader;
use Feenance\repositories\file_readers\FirstDirectCSVReader;

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
        return array_merge(["bank_balance"], parent::getExpectedFields());
    }

    public function testFirstDirectCSVFileReader()
    {
        $this->_test_I_can_create_an_importer_with_a_file_reader($this);
    }

    public function getExpectedRowCount()
    {
        return 25;
    }

};