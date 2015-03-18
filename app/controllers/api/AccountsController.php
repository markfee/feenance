<?php namespace Feenance\controllers\Api;

use Feenance\models\eloquent\Account;
use Feenance\repositories\EloquentAccountRepository;
use Markfee\Responder\Respond;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Feenance\Misc\Transformers\AccountsTransformer;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;

class AccountsController extends RestfulController {

  /* @var EloquentAccountRepository; */   protected $repository;
  function __construct(EloquentAccountRepository $repository) {
    parent::__construct($repository);
  }

//  protected function getTransformer() {    return $this->transformer ?: new AccountsTransformer;  }

  public function index()
  {
      $this->repository->paginate();
      return $this->respond();


        $records = Account::orderBy("open", "DESC")->paginate();
      return Respond::Paginated($records, $this->transformCollection($records->all()));
  }

  public function show($id)
  {
    try {
      $record = Account::findOrFail($id);
      return Respond::Raw($this->transform($record));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  /**
   * Add a new Account
   *
   * @return Respond
   */
  public function store()
  {
    $validator = Validator::make($data = Input::all(), Account::$rules);

    if ($validator->fails())		{
      return Respond::ValidationFailed();
    }

    $account = Account::create($data);
    return Respond::Raw($this->transform($account));
  }

  /**
   * Update a specific account.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
    $account = Account::findOrFail($id);
    $validator = Validator::make($data = $this->transformInput(Input::all()), Account::$rules);
    if ($validator->fails()) {
      return Respond::ValidationFailed();
    }

    $account->update($data);
    return Respond::Raw($this->transform($account));
  }

  /**
   * delete a specific account.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)	{
    try {
      if (! Account::destroy($id) ) {
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