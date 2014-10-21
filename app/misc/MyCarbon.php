<?php

namespace Markfee;
use \Carbon\Carbon;

class MyCarbon extends Carbon {

  /**
   * @param bool $includeToday
   * TODO: exclude bank holidays
   */
  public function previousWorkingDay($includeToday = false) {
    while( !($includeToday && $this->isWeekday()) ) {
      $this->subDay();
      $includeToday = true;
    }
  }

  /**
   * @param bool $includeToday
   * TODO: exclude bank holidays
   */
  public function nextWorkingDay($includeToday = false) {
    while( !($includeToday && $this->isWeekday()) ) {
      $this->addDay();
      $includeToday = true;
    }
  }



};