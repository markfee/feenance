<?php namespace Feenance\tests\unit\models;

use Feenance\tests\TestCase;
use Feenance\models\StandingOrder;


class StandingOrderTest extends TestCase {

  public function test_I_can_create_a_standing_order() {
    $standingorder = new StandingOrder();
  }

};