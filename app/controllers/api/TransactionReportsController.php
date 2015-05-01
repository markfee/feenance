<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 02/10/14
 * Time: 09:22
 */

namespace Feenance\controllers\Api;

use Feenance\models\eloquent\Transaction;
use Markfee\Responder\Respond;
use Markfee\Responder\Transformer;
//use Feenance\Misc\Transformers\TransactionReportTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
use \DB;
use \Carbon\Carbon;

class TransactionReportsController extends BaseController {

  function __construct() {
    parent::__construct();
    $this->resetQuery();
  }

  static function TRANSACTION_COUNT()   { return DB::raw('count(*) transaction_count'); }
//  static function RECONCILED_COUNT()    { return DB::raw('sum(reconciled) reconciled_count'); }
  static function TOTAL_CREDIT()  { return DB::raw('SUM(credit) credit_total'); }
  static function TOTAL_DEBIT()   { return DB::raw('SUM(debit) debit_total'); }
  static function TOTAL_NET()     { return DB::raw('SUM(movement) net_total'); }
//    static function YEAR()          { return DB::raw('YEAR(date) year'); }
//    static function MONTH()         { return DB::raw('DATE_FORMAT(date,"%Y-%m") month'); }
    static function YEAR()          { return DB::raw('FN_MY_YEAR(date) year'); }
//    static function MONTH()         { return DB::raw('CONCAT(FN_MY_YEAR(date), '-', FN_MY_MONTH(date)) month'); }
    static function MONTH()         { return DB::raw('fn_my_year_month(date) month'); }
  static function CATEGORY_ID()   { return DB::raw("COALESCE(category_id, 'UNKNOWN') category_id"); }
  /** @var \Illuminate\Database\Query\Builder $query */
  private $query;

  private function resetQuery() {
    $this->query = DB::table("v_non_transfers");
    $this->get   = [
      TransactionReportsController::TRANSACTION_COUNT(),
//      TransactionReportsController::RECONCILED_COUNT(),
      TransactionReportsController::TOTAL_CREDIT(),
      TransactionReportsController::TOTAL_DEBIT(),
      TransactionReportsController::TOTAL_NET(),
    ];
    return $this;
  }
  /* @return Transformer */
  /*
  public function getTransformer() {
    return $this->transformer ?: new TransactionReportTransformer;
  }
*/

  /**
   * All values can be null, but null parameters cannot come before non-null
   * ie filterDates(2014, 03, null, null) is valid (filter to march 2014
   * filterDates(2014, 03, 2015, 07) is valid (filter to between march 2014 and july 2015
   * filterDates(2014, 03, 2015, null) is valid (filter to between march 2014 and december 2015
   * filterDates(2014, null, 2015, null) is not valid
   * filterDates(2014, 01, 2015, null) use this instead to filter to between jan 2014 and dec 2015
   * @param $year
   * @param $month
   * @param $endYear
   * @param $endMonth
   * @return TransactionReportsController
   */
  private function filterDates($year, $month, $endYear, $endMonth=12) {
    if (empty($year)) {
      return $this;
    }

    if (empty($endYear)) {
      $this->query->where(DB::raw("YEAR(date)"), $year);
      if (! empty($month)) {
        $this->query->where(DB::raw("MONTH(date)"), $month);
      }
      return $this;
    }

    if ($endMonth == 12) {
      $endMonth = 1;
      $endYear++;
    } else {
      $endMonth++;
    }
    $startDate = Carbon::create($year, $month, 01, 0);
    $endDate   = Carbon::create($endYear, $endMonth, 01, 0);

    $this->query->where("date", ">=", $startDate)->where("date", "<", $endDate);
    return $this;
  }

  /**
   * @param $year
   * @return TransactionReportsController
   */
  private function filterYear($year) {
    return $this->filterDates($year, null, null, null);
  }
/*
  private function filterMonth($month) {
    $this->query = empty($month) ? $this->query : $this->query->where(DB::raw("MONTH(date)"), $month);
    return $this;
  }
*/

  /**
   * @param $category_id
   * @return TransactionReportsController
   */
  private function filterCategory($category_id) {
    $this->query = empty($category_id) ? $this->query : $this->query->where('category_id', $category_id);
    return $this;
  }


  /**
   * @return TransactionReportsController
   */
  private function withYear() {
    $this->query->groupBy("year")->orderBy("year");
    $this->get[] = TransactionReportsController::YEAR();
    return $this;
  }

  /**
   * @return TransactionReportsController
   */
  private function withMonth() {
    $this->query->groupBy("month")->orderBy("month");
    $this->get[] = TransactionReportsController::MONTH();
    return $this;
  }

  /**
   * @param null $category_id
   * @return TransactionReportsController
   */
  private function withCategory($category_id=null) {
    $this->filterCategory($category_id);
    $this->query->groupBy("category_id")->orderBy("category_id");
    $this->get[] = TransactionReportsController::CATEGORY_ID();
    return $this;
  }

  /**
   * @return array|static[]
   */
  private function getResults() {
    $results = $this->query->get($this->get);
    $this->resetQuery();
    return $results;
  }

  /**
   * @return \Illuminate\Http\JsonResponse
   */
  public function totals_by_year() {
    $total = $this->getResults()[0];
    $total->years = $this->withYear()->getResults();
    return Respond::Raw(["total"   => $total    ]);
  }

  /**
   * @param null $year
   * @param null $month
   * @return \Illuminate\Http\JsonResponse
   */
  public function totals_by_month($year = null, $month = null)
  {
    $total          = $this->filterYear($year)->withYear()->getResults()[0];
    $total->months  = $this->filterYear($year)->withYear()->withMonth()->getResults();
    return Respond::Raw(["total"   => $total    ]);
  }

  /**
   * @param null $year
   * @return \Illuminate\Http\JsonResponse
   */
  public function categories_by_year($year=null) {
    $total                    = $this->filterYear($year)->getResults()[0];

    if (empty($total->transaction_count)) {
      return Respond::NotFound("No transactions found for specified date range");
    }


    $yearSubTotals            = $this->filterYear($year)->withYear()->getResults();
    $categorySubTotals        = $this->filterYear($year)->withCategory()->getResults();
    $categoryByYearSubTotals  = $this->filterYear($year)->withCategory()->withYear()->getResults();

    $nested = Transformer::nest( [
        [$categorySubTotals,        "categories", "category_id"]
      , [$categoryByYearSubTotals,  "years",      "year"]
    ] );

    $total->categories = $nested["categories"];
    $total->years     = $yearSubTotals;
    return Respond::Raw([
      "total"          => $total
    ]);
  }

  /**
   * @param null $year
   * @param null $month
   * @param null $endYear
   * @param null $endMonth
   * @return \Illuminate\Http\JsonResponse
   */
  public function categories_by_month($year = null, $month = null, $endYear = null, $endMonth = null) {
    $total                      = $this->filterDates($year, $month, $endYear, $endMonth)->getResults()[0];

    if (empty($total->transaction_count)) {
      return Respond::NotFound("No transactions found for specified date range");
    }

    $monthSubTotals             = $this->filterDates($year, $month, $endYear, $endMonth)->withYear()->withMonth()->getResults();
    $categorySubTotals          = $this->filterDates($year, $month, $endYear, $endMonth)->withCategory()->getResults();
    $categoryByMonthSubTotals   = $this->filterDates($year, $month, $endYear, $endMonth)->withCategory()->withYear()->withMonth()->getResults();

    $nested = Transformer::nest( [
        [$categorySubTotals, "categories", "category_id"]
      , [$categoryByMonthSubTotals,   "months", "month"]
    ] );

    $total->categories = $nested["categories"];
    $total->months     = $monthSubTotals;
    return Respond::Raw([
      "total"          => $total
    ]);
 }
}