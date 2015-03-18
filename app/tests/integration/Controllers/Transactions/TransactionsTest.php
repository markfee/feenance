<?php namespace Feenance\tests;

use Illuminate\Http\Response;
use Carbon\Carbon;
use \SplFileObject;
use Feenance\controllers\Api\TransactionsController;
use Feenance\models\eloquent\Transaction;

class TransactionsTest extends TestCase {
    private $API_ROOT = "api/v1/transactions";

    public function test_index_returns_some_records() {
        $this->seed('AccountsTableSeeder');
        $this->seed('TransactionsTableSeeder');
        $response = $this->call('GET', $this->API_ROOT, [], [], array('HTTP_ACCEPT' => 'application/json'));
        $json = $this->assertValidJsonResponse($response, ['date', 'amount', 'account_id', 'reconciled', 'payee_id', 'category_id', 'notes', 'balance']);
    }

    public function test_add_new_transaction() {
        $this->seed('AccountsTableSeeder');
        $newTransaction = [
            "date" => Carbon::now(),
            "amount" => 10.25,
            "account_id" => 1,
            "reconciled" => true,
            "payee_id" => null,
            "category_id" => null,
            "notes" => "this is a test"
        ];

        $response = $this->call('POST', $this->API_ROOT, $newTransaction, [], array('HTTP_ACCEPT' => 'application/json'));
        $this->assertNoErrors($response->getData());
        $this->assertExpectedStatus(Response::HTTP_CREATED);
        $this->assertValidSingleRecordJsonResponse($response
            , [
                "id",
                "date",
                "amount",
                "account_id",
                "balance",
                "reconciled",
                "payee_id",
                "category_id",
                "notes",
                "source",
                "destination"
            ]
        );

    }

    public function test_adding_a_new_transaction_with_a_transfer_id_should_create_a_transfer() {
        $this->seed('AccountsTableSeeder');
        $newTransaction = [
            "date" => Carbon::now(),
            "amount" => 10.25,
            "account_id" => 1,
            "transfer_id" => 2,
            "reconciled" => true,
            "payee_id" => null,
            "category_id" => null,
            "notes" => "this is a test"
        ];

        $response = $this->call('POST', $this->API_ROOT, $newTransaction, [], array('HTTP_ACCEPT' => 'application/json'));
        $this->assertExpectedStatus(Response::HTTP_CREATED);

        $jsonResponse = $this->assertNRecordsResponse($response, 2
            , [
                "id",
                "date",
                "amount",
                "account_id",
                "balance",
                "reconciled",
                "payee_id",
                "category_id",
                "notes",
                "source",
                "destination"
            ]
        );
    }

    public function test_import_csv() {
        $this->runMigrations();
        $file = new SplFileObject("/home/mark/www/feenance/app/tests/integration/Controllers/Transactions/test_statement.csv", "r");
//    $controller = new TransactionsController();
        TransactionsController::uploadFile(1, $file);

        $response = $this->call('GET', $this->API_ROOT, [], [], array('HTTP_ACCEPT' => 'application/json'));
        $this->assertEquals($response->getData()->paginator->total, 25);
    }

    /*
     * When I post to bank_strings/{id}/transactions with a map object, I expect all transactions with that
     * bank_string_id to be updated, provided they do not already have some different map data applied.
     */
    public function test_map_update() {
        $this->runMigrations();
        \Feenance\models\eloquent\BankString::create(["account_id" => 1, "name" => "This will be updated"]);
        \Feenance\models\eloquent\BankString::create(["account_id" => 1, "name" => "This will not be updated"]);
        $transactionModel = new Transaction;
        $transactionModel->fillable(["date", "amount", "account_id", "reconciled", "payee_id", "category_id", "notes", "bank_string_id"]);

        $dummy_transaction_array = [
            "date" => Carbon::now(),
            "amount" => 1025,
            "account_id" => 1,
            "reconciled" => true,
            "payee_id" => null,
            "category_id" => null,
            "bank_string_id" => 1,
            "notes" => "this is a test"
        ];
        // Create two transactions with bank string 1

        $transactionModel->create($dummy_transaction_array);              /*1*/
        $transactionModel->create($dummy_transaction_array);              /*2*/

        /*Create a transactions with bank string 2*/

        $dummy_transaction_array["bank_string_id"] = 2;
        $transactionModel->create($dummy_transaction_array);              /*3*/

        /*Create another transactions with bank string 1 again but with a payee set*/

        $dummy_transaction_array["payee_id"] = 10;
        $dummy_transaction_array["bank_string_id"] = 1;
        $transactionModel->create($dummy_transaction_array);              /*4*/

        $map_update = [
            "account_id" => 1,
            "reconciled" => true,
            "payee_id" => 3,
            "category_id" => 4,
        ];

        $response = $this->call('POST', "api/v1/bank_strings/1/transactions", $map_update, [], array('HTTP_ACCEPT' => 'application/json'));
        $jsonResponse = $this->assertMultipleUpdate($response, 2);
//  Now check all of the updated records.
        $controller = new TransactionsController();

        $jsonResponse = $controller->index()->getData();

        array_map(function ($res) {
            if ($res->id == 1 || $res->id == 2) {
                $this->assertEquals($res->payee_id, 3, "Is the updated payee_id = 3? Actual result: " . $res->payee_id);
                $this->assertEquals($res->category_id, 4, "Is the updated category_id = 4? Actual result: " . $res->category_id);
            } else if ($res->id == 3) {
                $this->assertEquals(true, empty($res->payee_id), "Is the payee_id null? Actual result: " . $res->payee_id);
                $this->assertEquals(true, empty($res->category_id), "Is the category_id null? Actual result: " . $res->category_id);
            } else if ($res->id == 4) {
                $this->assertEquals(10, $res->payee_id, "Is the payee_id 10? Actual result: " . $res->payee_id);
                $this->assertEquals(true, empty($res->category_id), "Is the category_id null? Actual result: " . $res->category_id);
            }
        }, $jsonResponse->data);
    }
}

;

