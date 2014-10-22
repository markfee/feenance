<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 22/10/14
 * Time: 07:03
 */

namespace Feenance\tests\unit;
use Feenance\tests\TestCase;
use \Markfee\MyCarbon;


class MyCarbonTest extends TestCase {

  public function test_previous_day() {
    $date         = MyCarbon::create(2014, 8, 1, 0)->previousWorkingDay();
    $expectedDate = MyCarbon::create(2014, 7, 31, 0);

    $this->assertTrue(0 === $date->diff($expectedDate)->days);
  }
}
 