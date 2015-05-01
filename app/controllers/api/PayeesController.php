<?php

namespace Feenance\controllers\Api;

use Feenance\models\eloquent\Payee;
use Markfee\Responder\Respond;
use Feenance\Misc\Transformers\PayeeTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;

class PayeesController extends BaseController {

  /* @return Transformer */
  public function getTransformer() {    return $this->transformer ?: new PayeeTransformer;    }

  /**
   * Display a listing of payees
   *
   * @return Response
   */
  public function index()  {
    $payees = Payee::orderBy("name")->paginate();
    return Respond::Paginated($payees, $this->transformCollection($payees->all()));
  }

  /**
   * Display a specific payee.
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
      $payee = Payee::findOrFail($id);
      return Respond::Raw($this->transform($payee));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  /**
   * Search for Payees with name like $name
   *
   * @param  string $name
   * @return Response
   */
  public function search($name)  {
    $payees = Payee::where("name", "LIKE", "{$name}%")->orWhere("name", "like", "%{$name}%")->orderBy("name")->paginate();
    if ($payees->count() == 0) {
      return Respond::NotFound();
    }
    return Respond::Paginated($payees, $this->transformCollection($payees->all()));
  }

	/**
	 * Add a new Payee
	 *
	 * @return Respond
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Payee::$rules);

		if ($validator->fails())		{
			return Respond::ValidationFailed();
		}

    $payee = Payee::create($data);
    return Respond::Raw($this->transform($payee));
	}

	/**
	 * Update a specific payee.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$payee = Payee::findOrFail($id);
		$validator = Validator::make($data = Input::all(), Payee::$rules);
		if ($validator->fails()) {
      return Respond::ValidationFailed();
		}

    $payee->update($data);
    return Respond::Raw($this->transform($payee));
	}

	/**
	 * delete a specific payee.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)	{
    try {
      if (! Payee::destroy($id) ) {
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