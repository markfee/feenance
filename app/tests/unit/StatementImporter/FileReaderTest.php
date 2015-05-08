<?php namespace Feenance\tests\unit\StatementImporter;

use Feenance\Misc\Transformers\BankStringTransformer;
use Feenance\Misc\Transformers\TransactionTransformer;
use Feenance\repositories\EloquentBankStringRepository;
use Feenance\repositories\EloquentTransactionRepository;
use Feenance\Services\StatementImporter;

use Feenance\tests\TestCase;

abstract class FileReaderTest extends TestCase {

    protected static $EXPECTED_FIELDS = [];
    abstract public function getReader();
    abstract public function getExpectedRowCount();

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

    protected function _test_I_can_create_an_importer_with_a_file_reader($readerTest)
    {
        $reader = $readerTest->getReader();
        static::$EXPECTED_FIELDS = $readerTest->getExpectedFields();
        $repository = new EloquentTransactionRepository(new TransactionTransformer, new EloquentBankStringRepository(new BankStringTransformer()));

        $count = $repository->count()->getData();

        $statementImport = new StatementImporter($repository);
        $statementImport->importTransactionsToAccount(1, $reader);
        if ($statementImport->hasErrors()) {
            print_r($statementImport->getErrors());
        }

        $this->assertFalse($statementImport->hasErrors(), "Not expecting any errors");
        $newCount = $repository->count()->getData();
        $this->assertTrue($newCount > $count, "Test New Count ($newCount) > Count ($count)");

        $this->assertTrue(is_integer($batchId = $statementImport->getBatchId()), "statementImport Should return an integer Batch Id ({$batchId})");

        $expectedCount = $this->getExpectedRowCount();
        $calculatedCount = $newCount - $count;
        $this->assertTrue($expectedCount == $calculatedCount, "Expecting row counts to match: {$expectedCount} == {$calculatedCount}");
        $this->_test_batch_result_matches_expected_count($repository, $batchId, $expectedCount);
        $this->_test_results($repository, $batchId);
    }

    /**
     * @param EloquentTransactionRepository $repository
     * @param integer $batchId
     * @param integer $expectedCount
     */
    protected function _test_batch_result_matches_expected_count($repository, $batchId, $expectedCount)
    {
        $count = $repository->filterBatch($batchId)->count()->getData();
        $this->assertTrue(
            $expectedCount ==   $count
            , "Get batch result count ({$count}) does not match expected {$expectedCount} for batch {$batchId}"
        );
    }

    protected function _test_results($repository, $batchId)
    {
        $transactions = $repository->filterBatch($batchId)->paginate()->getData();
        foreach($transactions as $transaction) {
            $this->assertNonEmptyFields($transaction, static::$EXPECTED_FIELDS);
        }
    }


}