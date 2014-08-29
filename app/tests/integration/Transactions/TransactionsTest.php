<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 10:03
 */

use Illuminate\Http\Response;

class TransactionsTest extends TestCase {
  private $API_ROOT  = "api/v1/transactions";

  public function test_index_returns_some_records() {
    $response = $this->call('GET', $this->API_ROOT );
    $this->assertValidJsonResponse($response, ['date', 'amount', 'account_id', 'reconciled', 'payee_id', 'category_id', 'notes']);
  }

  public function test_index_returns_some_records_with_balance() {
    $response = $this->call('GET', $this->API_ROOT );
    $this->assertValidJsonResponse($response, ['balance']);
  }



};

