<?php
namespace api;
use \Account;
use Markfee\Responder\Respond;
class AccountsController extends BaseController {


  public function index()
  {
    $records = Account::paginate();
    return Respond::Paginated($records, $this->transformCollection($records->all()));
  }

  public function show($id)
  {
    $record = Account::findOrFail($id);
    return Respond::Raw($this->transform($record));
  }



}