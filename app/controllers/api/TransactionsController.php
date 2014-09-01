<?php
namespace api;

use \Transaction;
use Markfee\Responder\Respond;
use Misc\Transformers\TransactionTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransactionsController extends BaseController {

  /**
   * @return Transformer
  */
  protected function getTransformer() {
    return $this->transformer ?: new TransactionTransformer;
  }


	/**
	 * Display a listing of transactions
	 *
	 * @return Response
	 */
	public function index($account_id = null)
	{
    if (!empty($account_id)) {
      $records = Transaction::where("account_id", $account_id)->orderBy('date', "DESC")->paginate(100);
    } else
      $records = Transaction::orderBy('date', "DESC")->paginate(100);
    return Respond::Paginated($records, $this->transformCollection($records->all()));
	}

  /**
   * Display the specified transaction.
   *444
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    try {
      $transaction = Transaction::findOrFail($id);
    } catch(ModelNotFoundException $ex) {
      return Respond::NotFound("Transaction not found");
    }
    return Respond::Raw($this->transform($transaction));
  }


	/**
	 * Store a newly created transaction in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Transaction::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Transaction::create($data);

		return Redirect::route('transactions.index');
	}

	/**
	 * Show the form for editing the specified transaction.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$transaction = Transaction::find($id);

		return View::make('transactions.edit', compact('transaction'));
	}

	/**
	 * Update the specified transaction in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$transaction = Transaction::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Transaction::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$transaction->update($data);

		return Redirect::route('transactions.index');
	}

	/**
	 * Remove the specified transaction from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)	{
		Transaction::destroy($id);
		return Redirect::route('transactions.index');
	}

}
