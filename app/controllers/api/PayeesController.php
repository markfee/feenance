<?php

namespace api;
use \Payee;
use Markfee\Responder\Respond;
use Misc\Transformers\PayeeTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PayeesController extends BaseController {

  /* @return Transformer */
  protected function getTransformer() {    return $this->transformer ?: new PayeeTransformer;    }

  /**
   * Display a listing of payees
   *
   * @return Response
   */
  public function index()
  {
    $payees = Payee::orderBy("name")->paginate();
    return Respond::Paginated($payees, $this->transformCollection($payees->all()));
  }

  /**
   * Display the specified payee.
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
      $payee = Payee::findOrFail($id);
      return Respond::Raw($this->transform($payee));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  public function search($name)
  {
    $payees = Payee::where("name", "like", "%{$name}%")->paginate();
    return Respond::Paginated($payees, $this->transformCollection($payees->all()));
  }



	/**
	 * Store a newly created payee in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Payee::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Payee::create($data);

		return Redirect::route('payees.index');
	}

	/**
	 * Show the form for editing the specified payee.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$payee = Payee::find($id);

		return View::make('payees.edit', compact('payee'));
	}

	/**
	 * Update the specified payee in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$payee = Payee::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Payee::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$payee->update($data);

		return Redirect::route('payees.index');
	}

	/**
	 * Remove the specified payee from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Payee::destroy($id);

		return Redirect::route('payees.index');
	}

}
