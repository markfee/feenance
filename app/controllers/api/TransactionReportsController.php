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
use Feenance\Misc\Transformers\TransactionReportTransformer;
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
    $this->query = DB::table("transactions");
    $this->get   = [
      TransactionReportsController::TOTAL_CREDIT(),
      TransactionReportsController::TOTAL_DEBIT(),
      TransactionReportsController::TOTAL_NET(),
    ];
  }

  static function TOTAL_CREDIT()  { return DB::raw('SUM(IF(amount <= 0, null,  0.01 * 	amount)) total_credit'); }
  static function TOTAL_DEBIT()   { return DB::raw('SUM(IF(amount >= 0, null, -0.01 * 	amount)) total_debit'); }
  static function TOTAL_NET()     { return DB::raw('0.01 * SUM(amount) net_total'); }
  static function YEAR()          { return DB::raw('YEAR(date) year'); }
  static function MONTH()         { return DB::raw('MONTH(date) month'); }
  static function CATEGORY_ID()   { return DB::raw("IFNULL(category_id, 'UNKNOWN') category_id"); }

  /* @return Transformer */
  protected function getTransformer() {
    return $this->transformer ?: new TransactionReportTransformer;
  }

  private function filterYear($year) {
    static $APPLIED = false; if ($APPLIED) return $this->query; $APPLIED = true;
    return empty($year) ? $this->query : $this->query->where(DB::raw("YEAR(date)"), $year);
  }

  private function filterMonth($month) {
    static $APPLIED = false; if ($APPLIED) return $this->query; $APPLIED = true;
    return empty($month) ? $this->query : $this->query->where(DB::raw("MONTH(date)"), $month);
  }

  private function filterCategory($category_id) {
    static $APPLIED = false; if ($APPLIED) return $this->query; $APPLIED = true;
    return empty($category_id) ? $this->query : $this->query->where('category_id', $category_id);
  }


  private function withYear($year=null) {
    static $APPLIED = false; if ($APPLIED) return $this; $APPLIED = true;
    $this->filterYear($year)->groupBy("year")->orderBy("year");
    $this->get[] = TransactionReportsController::YEAR();
    return $this;
  }

  private function withMonth($month=null) {
    static $APPLIED = false; if ($APPLIED) return $this; $APPLIED = true;
    $this->filterMonth($month)->groupBy("month")->orderBy("month");
    $this->get[] = TransactionReportsController::MONTH();
    return $this;
  }

  private function withCategory($category_id=null) {
    static $APPLIED = false; if ($APPLIED) return $this; $APPLIED = true;
    $this->filterCategory($category_id)->groupBy("category_id")->orderBy("category_id");
    $this->get[] = TransactionReportsController::CATEGORY_ID();
    return $this;
  }

  private function getResults() {
    $query = clone($this->query); // Allows us to reuse query
    return $query->get($this->get);
  }

  private function getResultsByYear($year = null) {
    return $this->withYear($year)->getResults();
  }

  private function getResultsByMonth($year = null, $month = null) {
    return $this->withYear($year)->withMonth($month)->getResults();
  }


  public function totals_by_year() {
    return Respond::Raw([
      "total"   => $this->getResults(),
      "years"   => $this->getResultsByYear(),
    ]);
  }

  public function totals_by_month($year = null, $month = null)
  {
    return Respond::Raw([
      "total"   => $this->getResultsByYear($year),
      "months"  => $this->getResultsByMonth($year),
    ]);
  }

  public function categories_by_year($year=null) {
    return Respond::Raw([
      "grand_total"   => $this->getResults(),
      "categories"    => $this->withCategory()->getResults(),
      "years"         => $this->withYear($year)->getResults(),
    ]);
  }

  public function categories_by_month($year = null, $month = null) {
    $this->filterYear($year);
    $this->filterMonth($month);
    $grandTotal = $this->getResults()[0];
    $monthSubTotals             = $this->withYear($year)->withMonth($month)->getResults();
    $categorySubTotals          = $this->withCategory()->getResults();
    $monthSubTotalsWithCategory = $this->withCategory()->withYear($year)->withMonth($month)->getResults();

    $breakDown = Transformer::transformBy( [
        [$categorySubTotals, "categories", "category_id"]
      , [$monthSubTotalsWithCategory,   "months", "month"]
    ] );

    $grandTotal->categories = $breakDown["categories"];
    $grandTotal->months     = $monthSubTotals;
    return Respond::Raw([
      "totals"          => $grandTotal
    ]);
 }
}