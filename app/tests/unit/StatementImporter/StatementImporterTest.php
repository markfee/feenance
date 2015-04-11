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
    private static $EXPECTED_FIELDS = [];
    public function testAllReaders()
    {
        $this->_test_I_can_create_an_importer_with_a_file_reader((new FirstDirectFileReaderTest()));
        $this->_test_I_can_create_an_importer_with_a_file_reader((new TescoCSVFileReaderTest()));
    }

    private function _test_I_can_create_an_importer_with_a_file_reader($readerTest)
    {
        $reader = $readerTest->getReader();
        static::$EXPECTED_FIELDS = $readerTest->getExpectedFields();
        $repository = new EloquentTransactionRepository(new TransactionTransformer, new EloquentBankStringRepository(new BankStringTransformer()));

        $count = $repository->count()->getData();

        $statementImport = new StatementImporter($repository);
        $statementImport->importTransactionsToAccount(1, $reader);

        if ($statementImport->hasErrors()) {
            dd($statementImport->getErrors());
        }

        $this->assertFalse($statementImport->hasErrors());
        $newCount = $repository->count()->getData();
        $this->assertTrue($newCount > $count, "Test New Count ($newCount) > Count ($count)");

        $this->assertTrue(is_integer($batchId = $statementImport->getBatchId()), "statementImport Should return an integer Batch Id ({$batchId})");

        $expectedCount = $newCount - $count;
        $this->_test_batch_result_matches_expected_count($repository, $batchId, $expectedCount);
        $this->_test_results($repository, $batchId);
    }

    /**
     * @param EloquentTransactionRepository $repository
     * @param integer $batchId
     * @param integer $expectedCount
     */
    private function _test_batch_result_matches_expected_count($repository, $batchId, $expectedCount)
    {
        $count = $repository->filterBatch($batchId)->count()->getData();
        $this->assertTrue(
            $expectedCount ==   $count
            , "Get batch result count ({$count}) does not match expected {$expectedCount} for batch {$batchId}"
        );
    }

    private function _test_results($repository, $batchId)
    {
        $transactions = $repository->filterBatch($batchId)->paginate()->getData();
        foreach($transactions as $transaction) {
            $this->assertNonEmptyFields($transaction, static::$EXPECTED_FIELDS);
        }
    }

};