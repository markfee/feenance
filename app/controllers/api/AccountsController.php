<?php
namespace api;
use \Account;
use Markfee\Responder\Respond;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Misc\Transformers\AccountsTransformer;
class AccountsController extends BaseController {

  protected function getTransformer() {    return $this->transformer ?: new AccountsTransformer;  }

  public function index()
  {
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



}