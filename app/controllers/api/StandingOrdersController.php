<?php

namespace Feenance\Api;

use Feenance\Model\StandingOrder;
use Markfee\Responder\Respond;
use Feenance\Misc\Transformers\StandingOrderTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;

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
      $standingorders = StandingOrder::with("payee", "category.parent", "incrementUnit")->paginate();
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
		$validator = Validator::make($data = Input::all(), StandingOrder::$rules);

		if ($validator->fails())		{
			return Respond::ValidationFailed();
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

		$validator = Validator::make($data = Input::all(), StandingOrder::$rules);

		if ($validator->fails())
		{
      return Respond::ValidationFailed();
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
