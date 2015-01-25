<?php

namespace Feenance\tests;
use Feenance\Controllers\Api\CategoriesController;

class CategoriesTest extends TestCase {
  private $API_ROOT  = "api/v1/categories";

  public function test_controller_splits_two_sub_categories() {
    $category = "House: Insurance";

    $split = CategoriesController::splitCategory($category);

    $this->assertTrue(is_array($split), "Result should be an array");
    $this->assertEquals(2, count($split), "Result should be an array of length 2");
    $this->assertEquals("House", $split[0], "Arr[0] Should be House");
    $this->assertEquals("Insurance", $split[1], "Arr[1] Should be Insurance");
//    $this->seed('CategoriesTableSeeder');
//    $response = $this->call('GET', $this->API_ROOT ."/House: Mortgage");
//    $this->assertValidJsonResponse($response, ['name', 'acc_number', 'sort_code', 'notes', 'open', 'bank', 'opening_balance']);
  }
};

