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

    public function test_many_previous_days() {
    $date = MyCarbon::create(2014, 8, 1, 0);
    for ($i=0; $i < 12; $i++) {
      $expectedDate = clone($date);
      $expectedDate->previousWorkingDay();

//      print "\n" . $date . "(". $date->format("D") .  ") --- " . $expectedDate . "(". $expectedDate->format("D") .")";

      $this->assertTrue($date->diff($expectedDate)->days > 0, "Are the days different?");

      // Test that days are less than or equal to 5 days apart (in the case where there are two subsequent bank holidays next to a weekend)
      $this->assertTrue($date->diff($expectedDate)->days <= 5, "Are the days less than or equal to five apart?");

      $this->assertEquals(true, $expectedDate->isWeekday(), "Is Expected Day a weekday?");

      $date->addMonth();
    }
  }

    public function test_earliest() {
        $date1 = new MyCarbon("2015-08-01");
        $date2 = new MyCarbon("2015-08-10");
        $date3 = new MyCarbon("2016-08-10");

        $this->assertTrue( ($date1->earliest($date2)->isSameDay($date1)), "Expected {$date1} to be the earliest") ;
        $this->assertTrue( ($date2->earliest($date1)->isSameDay($date1)), "Expected {$date1} to be the earliest") ;

        $this->assertTrue( ($date3->earliest($date2)->isSameDay($date2)), "Expected {$date2} to be the earliest") ;

        $this->assertTrue( ($date3->earliest($date1)->isSameDay($date1)), "Expected {$date1} to be the earliest") ;

    }
};