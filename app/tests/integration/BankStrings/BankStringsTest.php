<?php

use Illuminate\Http\Response;
use Carbon\Carbon;
use api\BankStringsController;

class BankStringsTest extends TestCase {

  public function test_search_new_string_will_fail() {
    $controller = new BankStringsController();

    $response = $controller->searchExact("New String Doesn't Exist");
    $this->assertValidJsonError($response, Response::HTTP_NOT_FOUND);
  }

  public function test_passing_new_string_will_create_record() {
    $controller = new BankStringsController();

    $response = $controller->findOrCreate("New String Doesn't Exist");
    $this->assertExpectedStatus(Response::HTTP_CREATED);
    $this->assertValidSingleRecordJsonResponse($response, ["id", "name"]);
  }
}
