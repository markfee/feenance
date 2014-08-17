<?php

namespace MMEX;

class MmexController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
  public function accounts()
  {
    $record = Accounts::all();
    return \Response::json($record->all());
  }

  public function categories()
  {
    $record = Category::all();
    return \Response::json($record->all());
  }

  public function payees()
  {
    $record = Payee::all();
    return \Response::json($record->all());
  }

  public function standing_orders()
  {
    $record = StandingOrder::all();
    return \Response::json($record->all());
  }

  public function sub_categories()
  {
    $record = SubCategory::all();
    return \Response::json($record->all());
  }

  public function transactions()
  {
    $record = Transaction::all();
    return \Response::json($record->all());
  }


}
