<?php

namespace Feenance\controllers\Api;

use Feenance\models\eloquent\Category;
use Markfee\Responder\Respond;
use Feenance\Misc\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;
use \Paginator;

class CategoriesController extends BaseController {

  protected $paginateCount = 50;

  /* @return Transformer */
  public function getTransformer() {    return $this->transformer ?: new CategoryTransformer;    }

  /**
   * Display a listing of categories
   *
   * @return Response
   */
  public function index()
  {
      $categories = Category::with('parent')->paginate($this->paginateCount);
      return Respond::Paginated($categories, $this->transformCollection($categories->all()));
  }

  /**
   * @param String $str
   * Takes a string "House: Insurance" and returns an array = ["House", "Insurance"]
   * Takes a string "House Insurance" and returns an array = ["House Insurance"]
   */
  public static function splitCategory($str) {

    return array_map('trim', explode(':', $str));
  }

  /**
   * Display a specific category.
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
      $category = Category::findOrFail($id);
      return Respond::Raw($this->transform($category));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  /**
   * Search for category with name like $name
   *
   * @param  string $name
   * @return Response
   */
  public function search($name)  {
    $category = Category::with('parent')
      ->leftJoin('categories as parent', 'parent.id', '=', 'categories.parent_id')
      ->where("categories.name",    "LIKE", "{$name}%")
      ->orWhere("parent.name",      "LIKE", "%{$name}%")
      ->orWhere("categories.name",  "LIKE", "%{$name}%")
      ->orderBy("categories.name")
      ->get([
        'categories.id',
        'categories.name',
        'categories.parent_id'
      ])->all();
    if (count($category) == 0) {
      return Respond::NotFound();
    }
    $category = Paginator::make($category, 50);
    return Respond::Paginated($category, $this->transformCollection($category->all(), ["parent"]));
  }


	/**
	 * Add a new  category.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Category::$rules);

		if ($validator->fails())		{
			return Respond::ValidationFailed();
		}

		$category = Category::create($data);

		return Respond::Raw($this->transform($category));
	}

	/**
	 * Update a specific category.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$category = Category::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Category::$rules);

		if ($validator->fails())
		{
      return Respond::ValidationFailed();
		}

		$category->update($data);
    return Respond::Raw($this->transform($category));
	}

	/**
	 * Remove the specified category from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
    try {
      if (! Category::destroy($id) ) {
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
