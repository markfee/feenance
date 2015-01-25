<?php

namespace Feenance\controllers\Api;

use Feenance\Model\StandingOrder;
use Feenance\Model\Transaction;
use Feenance\Model\TransactionStatus;
use Markfee\Responder\Respond;
use Feenance\Misc\Transformers\StandingOrderTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;
use \Carbon\Carbon;

class StandingOrdersController extends BaseController {

  /* @return Transformer */
  protected function getTransformer() {    return $this->transformer ?: new StandingOrderTransformer;    }

  /**
   * Display a listing of standingorders
   *
   * @return Response
   */
  public function index()
  {
      $standingorders = StandingOrder::with("payee", "category.parent", "unit", "account", "destination")->paginate(100);
      return Respond::Paginated($standingorders, $this->transformCollection($standingorders->all()));
  }

  /**
   * Display a specific standingorder.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    if (!is_numeric($id)) {
      return $this->search($id);
    }
    try {
      $standingorder = StandingOrder::findOrFail($id);
      return Respond::Raw($this->transform($standingorder));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  /**
   * @param Carbon $date
   * @param $increment
   * @param $unit
   * @return mixed
   */
  private function incrementDate($date, $increment, $unit) {
    switch ($unit->id) {
      case "m":
        return $date->addMonths($increment);
      case "y":
        return $date->addYears($increment);
      case "w":
        return $date->addWeeks($increment);
      case "d":
        return $date->addDays($increment);
      default:
        return null;
    }
  }

  public function generateAll($endDate = null) {
    $unreconciled = TransactionsController::unreconciledCount();
    if ($unreconciled !== 0) {
      return Respond::ValidationFailed("You must reconcile all transactions prior to generating standing orders. Unreconciled: " . $unreconciled );
    }

    if (empty($endDate)) {
      $endDate = Carbon::now()->addYear();
    }

    $standingOrders = StandingOrder::all();
    Transaction::startBulk();

    foreach($standingOrders as $standingOrder) {
      $this->generateTransactions($standingOrder, $endDate);
    }
    Transaction::finishBulk(true);
  }

  /**
   * Increments a specific standing order by one payment
   * @param $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function increment($id)
  {
    try {
      /** @var \Illuminate\Support\Collection $standingOrder */
      $standingOrder = StandingOrder::findOrFail($id);
      $standingOrder->previous_date = $standingOrder->next_date;
      $standingOrder->next_date = $this->incrementDate($standingOrder->next_date, $standingOrder->increment, $standingOrder->unit);
      $standingOrder->save();
      return Respond::Raw($this->transform($standingOrder));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  /**
   * Generate Transactions for a specific standing order until $endDate
   *
   * @param  int    $id
   * @param  Date   $endDate default = 1 year from now
   * @return Response
   */
  public function generate($id, $endDate = null) {

    $unreconciled = TransactionsController::unreconciledCount();
    if ($unreconciled !== 0) {
      return Respond::ValidationFailed("You must reconcile all transactions prior to generating standing orders. Unreconciled: " . $unreconciled );
    }

    if (empty($endDate)) {
      $endDate = Carbon::now()->addYear();
    }

    try {
      $standingOrder = StandingOrder::findOrFail($id);
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }

    print "<br/>Start: " . $standingOrder->next_date . "\n";
    print "<br/>End  : " . $endDate . "\n";
    print "<br/>" . $standingOrder->frequency . "\n";


    Transaction::startBulk();

    $this->generateTransactions($standingOrder, $endDate);

    Transaction::finishBulk(true);

//    return ($this->show($id));
  }

  /**
   * @param $standingOrder
   * @param $endDate
   */
  private function generateTransactions($standingOrder, $endDate) {
    while ($standingOrder->next_date && $standingOrder->next_date < $endDate) {
      print "<br/>{$standingOrder->id}: " . $standingOrder->next_date . "\n";

      $transaction = Transaction::create([
          "date" => $standingOrder->next_date
        , "amount" => $standingOrder->amount
        , "account_id" => $standingOrder->account_id
        , "reconciled" => false
        , "status_id" => TransactionStatus::EXPECTED_STANDING_ORDER
        , "payee_id" => $standingOrder->payee_id
        , "category_id" => $standingOrder->category_id
      ]);
      $standingOrder->previous_date = $standingOrder->next_date;
      $standingOrder->next_date = $this->incrementDate($standingOrder->next_date, $standingOrder->increment, $standingOrder->unit);
    }
//    $standingOrder->save(); Don't save the standing order
    // It will be updated on reconcile rather than generate (otherwise we can't regenerate
    print "<br/>\n";
  }

  /**
   * Search for standingorder with name like $name
   *
   * @param  string $name
   * @return Response
   */
  public function search($name)  {
    $standingorder = StandingOrder::where("name", "LIKE", "{$name}%")->orWhere("name", "like", "%{$name}%")->orderBy("name")->paginate();
    if ($standingorder->count() == 0) {
      return Respond::NotFound();
    }
    return Respond::Paginated($standingorder, $this->transformCollection($standingorder->all()));
  }


	/**
	 * Add a new  standingorder.
	 *
	 * @return Response
	 */
	public function store()
	{
    $validator = Validator::make($data = $this->transformInput(Input::all()), StandingOrder::$rules);

		if ($validator->fails())		{
      return Respond::ValidationFailed($validator->getMessageBag()->first());
		}

		$standingorder = StandingOrder::create($data);

		return Respond::Raw($this->transform($standingorder));
	}

	/**
	 * Update a specific standingorder.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$standingorder = StandingOrder::findOrFail($id);

    $validator = Validator::make($data = $this->transformInput(Input::all()), StandingOrder::$rules);

		if ($validator->fails())
		{
      return Respond::ValidationFailed($validator->getMessageBag()->first());
		}

		$standingorder->update($data);
    return Respond::Raw($this->transform($standingorder));
	}

	/**
	 * Remove the specified standingorder from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
    try {
      if (! StandingOrder::destroy($id) ) {
          return Respond::NotFound();
      }
    } catch (QueryException $e) {
        return Respond::QueryException($e);
    } catch (Exception $e) {
      return Respond::InternalError($e->getMessage());
    }
		return Respond::Success();
	}

}
