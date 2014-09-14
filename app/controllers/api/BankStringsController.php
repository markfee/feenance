<?php

namespace api;

use \BankString;
use Markfee\Responder\Respond;
use Misc\Transformers\BankStringTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;

class BankStringsController extends BaseController {

  /* @return Transformer */
  protected function getTransformer() {    return $this->transformer ?: new BankStringTransformer;    }

  /**
   * Display a listing of bankstrings
   *
   * @return Response
   */
  public function index()
  {
      $bankstrings = BankString::paginate();
      return Respond::Paginated($bankstrings, $this->transformCollection($bankstrings->all()));
  }

  /**
   * Display a specific bankstring.
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
      $bankstring = BankString::findOrFail($id);
      return Respond::Raw($this->transform($bankstring));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  /**
   * Search for bankstring with name like $name
   *
   * @param  string $name
   * @return Response
   */
  public function search($name)  {
    $bankstring = BankString::where("name", "LIKE", "{$name}%")->orWhere("name", "like", "%{$name}%")->orderBy("name")->paginate();
    if ($bankstring->count() == 0) {
      return Respond::NotFound();
    }
    return Respond::Paginated($bankstring, $this->transformCollection($bankstring->all()));
  }

  /**
   * Search for bankstring with name == $name
   *
   * @param  string $name
   * @return Response
   */
  public function searchExact($name)  {
    try {
      $bankString = $this->searchExactOrFail($name);
      return Respond::Raw($this->transform($bankString));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  private function searchExactOrFail($name)  {
    return BankString::where("name", "=", "{$name}")->firstOrFail();
  }





  /**
	 * Add a new  bankstring.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), BankString::$rules);

		if ($validator->fails())		{
			return Respond::ValidationFailed();
		}

		$bankstring = BankString::create($data);

		return Respond::Raw($this->transform($bankstring));
	}

	/**
	 * Update a specific bankstring.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$bankstring = BankString::findOrFail($id);

		$validator = Validator::make($data = Input::all(), BankString::$rules);

		if ($validator->fails())
		{
      return Respond::ValidationFailed();
		}

		$bankstring->update($data);
    return Respond::Raw($this->transform($bankstring));
	}

	/**
	 * Remove the specified bankstring from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
    try {
      if (! BankString::destroy($id) ) {
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
