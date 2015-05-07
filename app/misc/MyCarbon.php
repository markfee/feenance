<?php

namespace Markfee;

use \Carbon\Carbon;

class MyCarbon extends Carbon
{

    /**
     * @param bool $includeToday
     * TODO: exclude bank holidays
     * @return MyCarbon
     */
    public function previousWorkingDay($includeToday = false)
    {
        while (!($includeToday && $this->isWeekday())) {
            $this->subDay();
            $includeToday = true;
        }
        return $this;
    }

    /**
     * @param bool $includeToday
     * TODO: exclude bank holidays
     * @return MyCarbon
     */
    public function nextWorkingDay($includeToday = false)
    {
        while (!($includeToday && $this->isWeekday())) {
            $this->addDay();
            $includeToday = true;
        }
        return $this;
    }

    /**
     * @param $increment integer
     * @param $unit string
     * @return MyCarbon
     */
    public function increment($increment, $unit)
    {
        switch ($unit) {
            case "m":
                return $this->addMonths($increment);
            case "y":
                return $this->addYears($increment);
            case "w":
                return $this->addWeeks($increment);
            case "d":
                return $this->addDays($increment);
            default:
                return null;
        }
    }
}

;