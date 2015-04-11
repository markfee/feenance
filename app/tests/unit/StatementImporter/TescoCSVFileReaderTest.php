<?php namespace Feenance\tests\unit\StatementImporter;

use Feenance\repositories\file_readers\BaseFileReader;
use Feenance\repositories\file_readers\TescoCSVReader;

class TescoCSVFileReaderTest extends FileReaderTest {

    public function getReader()
    {
        $file_path = base_path() . "/app/tests/unit/StatementImporter/test_tesco.csv";
        $reader = BaseFileReader::getReaderForFile($file_path);
        $this->assertTrue(($reader instanceof TescoCSVReader), "Expects an instance of TescoCSVReader");
        return $reader;
    }

    public function getExpectedFields()
    {
        return array_merge([], parent::getExpectedFields());
    }

};