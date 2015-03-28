<?php namespace Feenance\tests\unit\StatementImporter;

use Feenance\models\Transaction;
use Feenance\repositories\file_readers\FirstDirectCSVReader;
use Feenance\repositories\file_readers\TescoCSVReader;

class TescoCSVFileReaderTest extends FirstDirectFileReaderTest {

    public function getReader()
    {
        $file_path = base_path() . "/app/tests/unit/StatementImporter/test_tesco.csv";
        $reader = new TescoCSVReader();
        $reader->open($file_path);
        return $reader;

    }
};