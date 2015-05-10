<?php

namespace Markfee;

use \Carbon\Carbon;

class MyCarbon extends Carbon
{
    const YEAR_PATTERN = '/^[0-9]{4}$/';
    private static $monthNames = [
        "January"     => 1,
        "February"    => 2,
        "March"       => 3,
        "April"       => 4,
        "May"         => 5,
        "June"        => 6,
        "July"        => 7,
        "August"      => 8,
        "September"   => 9,
        "October"     => 10,
        "November"    => 11,
        "December"    => 12,
        "Jan"         => 1,
        "Feb"         => 2,
        "Mar"         => 3,
        "Apr"         => 4,
        "May"         => 5,
        "Jun"         => 6,
        "Jul"         => 7,
        "Aug"         => 8,
        "Sep"         => 9,
        "Oct"         => 10,
        "Nov"         => 11,
        "Dec"         => 12
    ];

    private static $dayNames = [
        "Sunday"    =>  0,
        "Monday"    =>  1,
        "Tuesday"   =>  2,
        "Wednesday" =>  3,
        "Thursday"  =>  4,
        "Friday"    =>  5,
        "Saturday"  =>  6,
        "Sun"       =>  0,
        "Mon"       =>  1,
        "Tue"       =>  2,
        "Wed"       =>  3,
        "Thu"       =>  4,
        "Fri"       =>  5,
        "Sat"       =>  6
    ];

    public function matchesMonth($string)
    {
        return (
            !empty(static::$monthNames[$string])
            &&  (static::$monthNames[$string] == $this->month)
        );
    }

    public function matchesDay($string)
    {
        return (
            !empty(static::$dayNames[$string])
            &&  (static::$dayNames[$string] == $this->dayOfWeek)
        );
    }

    public function matchesYear($string)
    {
        return ( preg_match(self::YEAR_PATTERN, $string) && (((int) $string) == $this->year) );
    }

    /**
     * match that will return true if the date matches a year, dayname or monthname
     * && is used to split strings that must ALL match
     * || is used to split strings that must match at least one
     * && takes precedence over ||
     * TODO: allow for brackets
     * @param $stringList
     * @return bool
     */
    public function fuzzyMatch($stringList)
    {
        $strings = explode("&&", $stringList);
        $failed = false;
        foreach($strings as $string) {
            $string = trim($string);
            if ( ! $this->fuzzyMatchOneOf($string)) {
                return false;
            }
        }
        return true;
    }

    public function fuzzyMatchOneOf($stringList)
    {
        $strings = explode("||", $stringList);
        foreach($strings as $string) {
            $string = trim($string);
            if ($this->matchesDay($string) || $this->matchesMonth($string)  || $this->matchesYear($string)) {
                return true;
            }
        }
        return false;
    }

    public function earliest($date)
    {
        return empty($date) || ($this < $date) ? $this : clone($date);
    }

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