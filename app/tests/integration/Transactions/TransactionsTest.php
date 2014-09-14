<?php

use Illuminate\Http\Response;
use Carbon\Carbon;
use \SplFileObject;
use \api\TransactionsController;

class TransactionsTest extends TestCase {
  private $API_ROOT  = "api/v1/transactions";

  public function test_index_returns_some_records() {
    $this->seed('AccountsTableSeeder');
    $this->seed('TransactionsTableSeeder');
    $response = $this->call('GET', $this->API_ROOT );
//    dd($response);
    $json = $this->assertValidJsonResponse($response, ['date', 'amount', 'account_id', 'reconciled', 'payee_id', 'category_id', 'notes', 'balance']);
  }

  public function test_add_new_transaction() {
    $this->seed('AccountsTableSeeder');
    $newTransaction = [
      "date"              =>  Carbon::now(),
      "amount"            =>  10.25,
      "account_id"        =>  1,
      "reconciled"        =>  true,
      "payee_id"          =>  null,
      "category_id"       =>  null,
      "notes"             =>  "this is a test"
    ];

    $response = $this->call('POST', $this->API_ROOT, $newTransaction);
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
      "date"              =>  Carbon::now(),
      "amount"            =>  10.25,
      "account_id"        =>  1,
      "transfer_id"       =>  2,
      "reconciled"        =>  true,
      "payee_id"          =>  null,
      "category_id"       =>  null,
      "notes"             =>  "this is a test"
    ];

    $response = $this->call('POST', $this->API_ROOT, $newTransaction);
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
    $this->seed('AccountsTableSeeder');
    $file = new SplFileObject("/home/mark/www/feenance/app/tests/integration/Transactions/test_statement.csv", "r");
    $controller = new TransactionsController();
    $controller->uploadFile(1, $file);

    $response = $controller->index();
    $this->assertEquals($response->getData()->paginator->total, 25);

    dd($response->getData());
  }
};

