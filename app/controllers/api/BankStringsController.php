<?php

namespace Feenance\Api;

use Feenance\Model\BankString;
use Markfee\Responder\Respond;
use Feenance\Misc\Transformers\BankStringTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;

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
      $bankStrings = BankString::paginate();
      return Respond::Paginated($bankStrings, $this->transformCollection($bankStrings->all()));
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
      $bankString = BankString::findOrFail($id);
      return Respond::Raw($this->transform($bankString));
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
    $bankString = BankString::where("name", "LIKE", "{$name}%")->orWhere("name", "like", "%{$name}%")->orderBy("name")->paginate();
    if ($bankString->count() == 0) {
      return Respond::NotFound();
    }
    return Respond::Paginated($bankString, $this->transformCollection($bankString->all()));
  }

  /**
   * Search for bankstring with name == $name
   *
   * @param  string $name
   * @return Response
   */
  public function searchExact($account_id, $name) {
    try {
      $bankString = $this->searchExactOrFail($account_id, $name);
      return Respond::Raw($this->transform($bankString));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  private function searchExactOrFail($account_id, $name)  {
    return BankString::where("name", "=", "{$name}")->where("account_id", "=", $account_id)->firstOrFail();
  }

  /**
   * Search for bankstring with name == $name
   *
   * @param  string $name
   * @return Response
   */
  public function findOrCreate($account_id, $name)  {
    try {
      $bankString = $this->searchExactOrFail($account_id, $name);
      return Respond::Raw($this->transform($bankString));
    } catch (ModelNotFoundException $e) {
      $bankString = BankString::create( [ "account_id" => $account_id, "name" => $name ] );
      Respond::setStatusCode(ResponseCodes::HTTP_CREATED);
      return Respond::Raw($this->transform($bankString));
    }
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

    $bankString = BankString::create($data);

		return Respond::Raw($this->transform($bankString));
	}

	/**
	 * Update a specific bankstring.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$bankString = BankString::findOrFail($id);

		$validator = Validator::make($data = Input::all(), BankString::$rules);

		if ($validator->fails())
		{
      return Respond::ValidationFailed();
		}

		$bankString->update($data);
    return Respond::Raw($this->transform($bankString));
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
