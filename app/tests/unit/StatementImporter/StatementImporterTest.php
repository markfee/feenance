<?php namespace Feenance\tests\unit\StatementImporter;
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 22/10/14
 * Time: 07:03
 */

use Feenance\Misc\Transformers\BankStringTransformer;
use Feenance\Misc\Transformers\TransactionTransformer;
use Feenance\repositories\EloquentBankStringRepository;
use Feenance\repositories\EloquentTransactionRepository;
use Feenance\repositories\file_readers\FirstDirectCSVReader;
use Feenance\tests\TestCase;
use Feenance\Services\StatementImporter;

class StatementImporterTest extends TestCase {

    public function test_exists() {
    $file = new \SplFileObject(base_path() . "/app/tests/unit/StatementImporter/test_firstdirect.csv", "r");
    $file = new \SplFileObject(base_path() . "/app/tests/unit/StatementImporter/test_tesco.csv", "r");
  }

//  public function test_will_import_a_file_to_an_account() {
//    $file = new \SplFileObject(base_path() . "/app/tests/unit/StatementImporter/test_tesco.csv", "r");
//    $statementImport = new StatementImporter(1, $file);
//    $this->assertTrue($statementImport)
//  }

    private function _test_I_can_create_an_importer_with_a_file_reader($reader)
    {
        $repository = new EloquentTransactionRepository(new TransactionTransformer, new EloquentBankStringRepository(new BankStringTransformer()));

        $count = $repository->count()->getData();

        $statementImport = new StatementImporter($repository);
        $statementImport->importTransactionsToAccount(1, $reader);

        if ($statementImport->hasErrors()) {
            dd($statementImport->getJsonErrors());
        }

        $this->assertFalse($statementImport->hasErrors());
        $newCount = $repository->count()->getData();
        $this->assertTrue($newCount > $count);
    }

    public function testAllReaders()
    {
        $this->_test_I_can_create_an_importer_with_a_file_reader((new FirstDirectFileReaderTest)->getReader());

    }

};