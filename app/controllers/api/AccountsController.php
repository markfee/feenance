<?php
namespace api;
class AccountsController extends \BaseController {


  public function index()
  {
    $records = Account->paginate();
    $transformer = new TransactionTransformer;
    return Respond::Paginated($records, $transformer->transformCollection($records->all()));
  }

}