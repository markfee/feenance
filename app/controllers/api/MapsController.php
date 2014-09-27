<?php

namespace Feenance\Api;

use \Map;
use Markfee\Responder\Respond;
use Feenance\Misc\Transformers\MapTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;

class MapsController extends BaseController {

  /* @return Transformer */
  protected function getTransformer() {    return $this->transformer ?: new MapTransformer;    }

  /**
   * Display a listing of maps
   *
   * @return Response
   */
  public function index()
  {
      $maps = Map::paginate();
      return Respond::Paginated($maps, $this->transformCollection($maps->all()));
  }

  /**
   * Display a specific map.
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
      $map = Map::findOrFail($id);
      return Respond::Raw($this->transform($map));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  /**
   * Search for map with name like $name
   *
   * @param  string $name
   * @return Response
   */
  public function search($name)  {
    $map = Map::where("name", "LIKE", "{$name}%")->orWhere("name", "like", "%{$name}%")->orderBy("name")->paginate();
    if ($map->count() == 0) {
      return Respond::NotFound();
    }
    return Respond::Paginated($map, $this->transformCollection($map->all()));
  }


	/**
	 * Add a new  map.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Map::$rules);

		if ($validator->fails())		{
			return Respond::ValidationFailed();
		}
    try {
      $map = Map::create($data);
      return Respond::Raw($this->transform($map));
    } catch (QueryException $e) {
    return Respond::QueryException($e);
    } catch (Exception $e) {
      return Respond::InternalError($e->getMessage());
    }
	}

	/**
	 * Update a specific map.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$map = Map::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Map::$rules);

		if ($validator->fails())
		{
      return Respond::ValidationFailed();
		}

		$map->update($data);
    return Respond::Raw($this->transform($map));
	}

	/**
	 * Remove the specified map from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
    try {
      if (! Map::destroy($id) ) {
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
