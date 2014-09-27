<?php

use Illuminate\Http\Response;
use Carbon\Carbon;
use Feenance\Api\BankStringsController;

class BankStringsTest extends TestCase {

/*  public function test_search_new_string_will_fail() {
    $controller = new BankStringsController();

    $response = $controller->searchExact(1, "New String Doesn't Exist");
    $this->assertValidJsonError($response, Response::HTTP_NOT_FOUND);
  }*/

  public function test_passing_new_string_will_create_record_or_return_existing_one() {

    $bank_string = BankString::findOrCreate(123, "New String Doesn't Exist")->firstOrFail();
    $this->assertEquals($bank_string->id, 1);
    $this->assertEquals($bank_string->name, "New String Doesn't Exist");
    $this->assertEquals($bank_string->account_id, 123);

    $bank_string = BankString::findOrCreate(123, "Different String Doesn't Exist")->firstOrFail();
      $this->assertEquals($bank_string->id, 2);
      $this->assertEquals($bank_string->name, "Different String Doesn't Exist");
      $this->assertEquals($bank_string->account_id, 123);

    $bank_string = BankString::findOrCreate(123, "New String Doesn't Exist")->firstOrFail();
    $this->assertEquals($bank_string->id, 1);
      $this->assertEquals($bank_string->name, "New String Doesn't Exist");
      $this->assertEquals($bank_string->account_id, 123);

  }

}
