<?php namespace Feenance\tests\unit\StatementImporter;


use Feenance\Misc\Transformers\BankStringTransformer;
use Feenance\Misc\Transformers\TransactionTransformer;
use Feenance\repositories\EloquentBankStringRepository;
use Feenance\repositories\EloquentTransactionRepository;
use Feenance\tests\TestCase;
use Carbon\Carbon;

class TransactionRepositoryTest extends TestCase {

    /** @var EloquentTransactionRepository $repository */
    private $repository;

    function __construct()
    {
        $this->repository = new EloquentTransactionRepository(new TransactionTransformer, new EloquentBankStringRepository(new BankStringTransformer()));
    }

    public function testRepositoryNotNull()
    {
        $this->assertTrue($this->repository instanceof EloquentTransactionRepository);
    }

    public function testAddTransactionWithBankStringGetsCorrectCategoryAndPayee()
    {
        $newTransaction = [
            "date" => Carbon::now(),
            "amount" => 10.25,
            "account_id" => 1000,
            "payee_id" => 7,
            "category_id" => 6,
            "bank_string" => "I should have a payee id of 7 and a category of 6",
        ];

        $result = $this->repository->create($newTransaction)->getData();
        $this->assertTrue($result["payee_id"] == 7, "payee_id should be 7 - {$result['payee_id']}");
        $this->assertTrue($result["category_id"] == 6, "category_id should be 6 - {$result['category_id']}");

        $anotherTransactionWithSameString = [
            "date" => Carbon::now(),
            "amount" => 10.25,
            "account_id" => 1000,
            "bank_string" => "I should have a payee id of 7 and a category of 6",
        ];

        $result = $this->repository->create($anotherTransactionWithSameString)->getData();
        $this->assertTrue($result["category_id"] == 6, "category_id should be 6 - {$result['category_id']}");
        $this->assertTrue($result["payee_id"] == 7, "payee_id should be 7 - {$result['payee_id']}");

        $aTransactionWithSameStringButPreSetCategories = [
            "date" => Carbon::now(),
            "amount" => 10.25,
            "account_id" => 1000,
            "payee_id" => 8,
            "category_id" => 9,
            "bank_string" => "I should have a payee id of 7 and a category of 6",
        ];

        $result = $this->repository->create($aTransactionWithSameStringButPreSetCategories)->getData();
        $this->assertTrue($result["payee_id"] == 8, "payee_id should be 8 - {$result['payee_id']}");
        $this->assertTrue($result["category_id"] == 9, "category_id should be 9 - {$result['category_id']}");


    }

}