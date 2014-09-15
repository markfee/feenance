<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 10:03
 */

use Illuminate\Http\Response;

class AccountsTest extends TestCase {
  private $API_ROOT  = "api/v1/accounts";

  public function test_index_returns_some_records() {
    $this->seed('AccountsTableSeeder');
    $response = $this->call('GET', $this->API_ROOT, [], [], array('HTTP_ACCEPT' => 'application/json') );
    $this->assertValidJsonResponse($response, ['name', 'acc_number', 'sort_code', 'notes', 'open', 'bank', 'opening_balance']);
  }
};