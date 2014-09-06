<?php

namespace MMEX;

class MmexController extends \api\BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
  public function accounts()
  {
    $records = Accounts::all();
    return \Response::json($records->all());
  }

  public function categories()
  {
    $records = Category::all();
    return \Response::json($records->all());
  }

  public function payees()
  {
    $records = Payee::all();
    return \Response::json($records->all());
  }

  public function standing_orders()
  {
    $records = StandingOrder::all();
    return \Response::json($records->all());
  }

  public function sub_categories()
  {
    $records = SubCategory::all();
    return \Response::json($records->all());
  }

  public function transactions()
  {
    $records = Transaction::all();
    return \Response::json($records->all());
  }


}
