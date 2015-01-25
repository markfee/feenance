<?php

namespace Feenance\Controllers\Api;

use \TransactionStatus;
use Markfee\Responder\Respond;
use Feenance\Misc\Transformers\TransactionStatusTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;

class TransactionStatusesController extends BaseController {

  /* @return Transformer */
  protected function getTransformer() {    return $this->transformer ?: new TransactionStatusTransformer;    }

  /**
   * Display a listing of transactionstatuses
   *
   * @return Response
   */
  public function index()
  {
      $transactionStatuses = TransactionStatus::paginate();
      return Respond::Paginated($transactionStatuses, $this->transformCollection($transactionStatuses->all()));
  }

  /**
   * Display a specific transactionstatus.
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
      $transactionStatus = TransactionStatus::findOrFail($id);
      return Respond::Raw($this->transform($transactionStatus));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  /**
   * Search for transactionstatus with name like $name
   *
   * @param  string $name
   * @return Response
   */
  public function search($name)  {
    $transactionStatus = TransactionStatus::where("name", "LIKE", "{$name}%")->orWhere("name", "like", "%{$name}%")->orderBy("name")->paginate();
    if ($transactionStatus->count() == 0) {
      return Respond::NotFound();
    }
    return Respond::Paginated($transactionStatus, $this->transformCollection($transactionStatus->all()));
  }


	/**
	 * Add a new  transactionstatus.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), TransactionStatus::$rules);

		if ($validator->fails())		{
			return Respond::ValidationFailed();
		}

		$transactionStatus = TransactionStatus::create($data);

		return Respond::Raw($this->transform($transactionStatus));
	}

	/**
	 * Update a specific transactionstatus.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$transactionStatus = TransactionStatus::findOrFail($id);

		$validator = Validator::make($data = Input::all(), TransactionStatus::$rules);

		if ($validator->fails())
		{
      return Respond::ValidationFailed();
		}

		$transactionStatus->update($data);
    return Respond::Raw($this->transform($transactionStatus));
	}

	/**
	 * Remove the specified transactionstatus from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
    try {
      if (! TransactionStatus::destroy($id) ) {
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
