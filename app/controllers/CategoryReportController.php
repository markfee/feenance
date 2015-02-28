<?php namespace Feenance;

use Feenance\controllers\Api\BaseController;

class CategoryReportController extends BaseController {
  public static function category_report($year=null, $month=null, $endYear=null, $endMonth=null) {
    return \View::make('categories_report')
      ->with("year", $year)
      ->with("month", $month)
      ->with("endYear", $endYear)
      ->with("endMonth", $endMonth)
      ;
  }
}