<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 07/09/14
 * Time: 07:44
 */

class CategoriesTest extends TestCase {
  private $API_ROOT  = "api/v1/categories";

  public function test_contoller_returns_sub_categories() {
    $this->seed('CategoriesTableSeeder');
    $response = $this->call('GET', $this->API_ROOT ."/House: Mortgage");
//    $this->assertValidJsonResponse($response, ['name', 'acc_number', 'sort_code', 'notes', 'open', 'bank', 'opening_balance']);
  }
};

