<?php

use Illuminate\Http\Response;
use Carbon\Carbon;
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

    $this->assertResponseStatus(Response::HTTP_CREATED);
  }



  };

