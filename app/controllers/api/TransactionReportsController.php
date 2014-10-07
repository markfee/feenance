<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 02/10/14
 * Time: 09:22
 */

namespace Feenance\Api;

use Feenance\Model\Transaction;
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

  static function TOTAL_CREDIT()  { return DB::raw('SUM(IF(amount <= 0, null,  0.01 * 	amount)) credit_total'); }
  static function TOTAL_DEBIT()   { return DB::raw('SUM(IF(amount >= 0, null, -0.01 * 	amount)) debit_total'); }
  static function TOTAL_NET()     { return DB::raw('0.01 * SUM(amount) net_total'); }
  static function YEAR()          { return DB::raw('YEAR(date) year'); }
  static function MONTH()         { return DB::raw('MONTH(date) month'); }
  static function CATEGORY_ID()   { return DB::raw("IFNULL(category_id, 'UNKNOWN') category_id"); }
  private $query;

  private function resetQuery() {
    $this->query = DB::table("transactions");
    $this->get   = [
      TransactionReportsController::TOTAL_CREDIT(),
      TransactionReportsController::TOTAL_DEBIT(),
      TransactionReportsController::TOTAL_NET(),
    ];
    return $this;
  }
  /* @return Transformer */
  /*
  protected function getTransformer() {
    return $this->transformer ?: new TransactionReportTransformer;
  }
*/

  private function filterYear($year) {
    $this->query = empty($year) ? $this->query : $this->query->where(DB::raw("YEAR(date)"), $year);
    return $this;
  }

  private function filterMonth($month) {
    $this->query = empty($month) ? $this->query : $this->query->where(DB::raw("MONTH(date)"), $month);
    return $this;
  }

  private function filterCategory($category_id) {
    $this->query = empty($category_id) ? $this->query : $this->query->where('category_id', $category_id);
    return $this;
  }


  private function withYear($year=null) {
    $this->filterYear($year);
    $this->query->groupBy("year")->orderBy("year");
    $this->get[] = TransactionReportsController::YEAR();
    return $this;
  }

  private function withMonth($month=null) {
    $this->filterMonth($month);
    $this->query->groupBy("month")->orderBy("month");
    $this->get[] = TransactionReportsController::MONTH();
    return $this;
  }

  private function withCategory($category_id=null) {
    $this->filterCategory($category_id);
    $this->query->groupBy("category_id")->orderBy("category_id");
    $this->get[] = TransactionReportsController::CATEGORY_ID();
    return $this;
  }

  private function getResults() {
    $results = $this->query->get($this->get);
    $this->resetQuery();
    return $results;
  }

  public function totals_by_year() {
    $total = $this->getResults()[0];
    $total->years = $this->withYear()->getResults();
    return Respond::Raw(["total"   => $total    ]);
  }

  public function totals_by_month($year = null, $month = null)
  {
    $total          = $this->withYear($year)->getResults()[0];
    $total->months  = $this->withYear($year)->withMonth($month)->getResults();
    return Respond::Raw(["total"   => $total    ]);
  }

  public function categories_by_year($year=null) {
    $total                    = $this->filterYear($year)->getResults()[0];
    $yearSubTotals            = $this->withYear($year)->getResults();
    $categorySubTotals        = $this->filterYear($year)->withCategory()->getResults();
    $categoryByYearSubTotals  = $this->withCategory()->withYear($year)->getResults();

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

  public function categories_by_month($year = null, $month = null) {
    $total                      = $this->filterYear($year)->filterMonth($month)->getResults()[0];
    $monthSubTotals             = $this->withYear($year)->withMonth($month)->getResults();
    $categorySubTotals          = $this->filterYear($year)->filterMonth($month)->withCategory()->getResults();
    $categoryByMonthSubTotals   = $this->withCategory()->withYear($year)->withMonth($month)->getResults();

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