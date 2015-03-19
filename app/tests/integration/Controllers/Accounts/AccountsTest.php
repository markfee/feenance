<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 10:03
 */

namespace Feenance\tests;

use Illuminate\Http\Response;

class AccountsTest extends TestCase {
  private $API_ROOT  = "api/v1/accounts";

    public function test_index_returns_some_records() {
        $this->seed('AccountsTableSeeder');
        $response = $this->call('GET', $this->API_ROOT, [], [], array('HTTP_ACCEPT' => 'application/json') );
        $this->assertValidJsonResponse($response,
            ['name', 'acc_number', 'sort_code', 'notes', 'open', 'bank', 'opening_balance']);
    }

    public function test_index_with_id_returns_some_records() {
        $this->seed('AccountsTableSeeder');
        $response = $this->call('GET', $this->API_ROOT ."/1", [], [], array('HTTP_ACCEPT' => 'application/json') );
        $this->assertExpectedStatus(Response::HTTP_FOUND);
        $this->assertValidSingleRecordJsonResponse($response,
            ['name', 'acc_number', 'sort_code', 'notes', 'open', 'bank', 'opening_balance']);
    }

    public function test_add_new_account() {
        $this->seed('AccountsTableSeeder');
        $newAccount = [
            "name" => "My Account",
            "acc_number" => "1234554",
            "sort_code" => "20-23-10",
            "notes" => "Test Account",
            "open" => true,
            "bank" => "Bank of Test",
            "opening_balance" => "123.32"
        ];

        $response = $this->call('POST', $this->API_ROOT, $newAccount, [], array('HTTP_ACCEPT' => 'application/json'));
        $this->assertNoErrors($response->getData());
        $this->assertExpectedStatus(Response::HTTP_CREATED);
        $this->assertValidSingleRecordJsonResponse($response,
            ['name', 'acc_number', 'sort_code', 'notes', 'open', 'bank', 'opening_balance']);
    }

    public function test_update_account() {
        $this->runMigrations();
        $this->seed('AccountsTableSeeder');
        $get_response = $this->call('GET', $this->API_ROOT ."/1", [], [], array('HTTP_ACCEPT' => 'application/json') );
        $get_response_data = $get_response->getData();
        $newName = "This is a test";
        $this->assertFalse(0 === strcmp($newName, $get_response_data ->name),
            "Name should not match new name otherwise we aren't testing anything");

        $get_response_data->name = $newName;
        // call refreshApplication to allow a second http request.
        $this->refreshApplication();
        $post_response = $this->call('PUT', $this->API_ROOT ."/1", (Array) $get_response_data, [], array('HTTP_ACCEPT' => 'application/json') );
        $post_response_data = $post_response->getData();

        $this->assertValidSingleRecordJsonResponse($post_response,
            ['name', 'acc_number', 'sort_code', 'notes', 'open', 'bank', 'opening_balance']);

        $this->assertTrue(0 === strcmp($newName, $post_response_data->name), "Name should match new name");
    }

    public function test_delete_account() {
        $response = $this->call('DELETE', $this->API_ROOT ."/1", [], [], array('HTTP_ACCEPT' => 'application/json') );
        $this->assertNoErrors($response->getData());
        $this->assertExpectedStatus(Response::HTTP_OK);
//        dd($response);
        $this->refreshApplication();
        $get_response = $this->call('GET', $this->API_ROOT ."/1", [], [], array('HTTP_ACCEPT' => 'application/json') );
        $this->assertExpectedStatus(Response::HTTP_NOT_FOUND);
    }
 };