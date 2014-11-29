<?php

namespace Feenance\Api;

use Markfee\Responder\Respond;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
//use Feenance\Misc\Transformers\TransferTransformer;
use Feenance\Model\Transfer;
use Feenance\Model\PotentialTransfer;
use Feenance\Model\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;

class TransfersController extends BaseController {

  /* @return Transformer */
//  protected function getTransformer() {    return $this->transformer ?: new TransferTransformer;    }

  /**
   * Display a listing of transfers
   *
   * @return Response
   */
  public function index()
  {
      $transfers = Transfer::paginate();
      return Respond::Paginated($transfers, $this->transformCollection($transfers->all()));
  }

  /**
   * Display a specific transfer.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    try {
      $transfer = Transfer::where("source", $id)->orWhere("destination", $id)->firstOrFail();
      return Respond::Raw($this->transform($transfer));
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    }
  }

  /**
   * expects a POST { source: id, destination: id }
   * creates a transfer if the two transactions exist
   * and the dates are the same
   * and the accounts don't match
   * and the amounts are the same (with opposite signs)
   * @return \Illuminate\Http\JsonResponse
   */
  public function joinTwoTransactionsAsTransfer()
  {
    $validator = Validator::make($data = $this->transformInput(Input::all()), Transfer::$rules);

    if ($validator->fails())		{
      Respond::WithErrors($validator->getMessageBag());
      return Respond::ValidationFailed();
    }

    $sourceId       = $data["source"];
    $destinationId  = $data["destination"];

    try {
      /** @var Transaction $sourceTransaction */
      $sourceTransaction = Transaction::findOrFail($sourceId);
      /** @var Transaction $destinationTransaction */
      $destinationTransaction = Transaction::findOrFail($destinationId);
    } catch(ModelNotFoundException $ex) {
      return Respond::NotFound("Transaction not found for transfer");
    }

    try {
      if ($sourceTransaction->account_id == $destinationTransaction->account_id) {
        return Respond::ValidationFailed("Source and Destination accounts must be different");
      }

      /** @var Carbon $srcDate */
      $srcDate = $sourceTransaction->date;
      /** @var Carbon $destinationDate */
      $destinationDate = $destinationTransaction->date;

      if (0 !== $srcDate->diffInDays($destinationDate)) {
        return Respond::ValidationFailed("Source and destination transactions must be made on the same day");
      }


      if ($sourceTransaction->amount + $destinationTransaction->amount !== 0) {
        return Respond::ValidationFailed("Source amount must be equal to minus the destination amount");
      }

      if ($sourceTransaction->amount > 0) {
        return Respond::ValidationFailed("Source amount must be negative");
      }

      $transfer = Transfer::create($data);
      Respond::setStatusCode(ResponseCodes::HTTP_CREATED);
      return Respond::Raw($this->transform($transfer));

    } catch (QueryException $e) {
      return Respond::QueryException($e);
    } catch (\Exception $ex) {
      return Respond::ValidationFailed($ex->getMessage());
    }
  }

  /**
   * returns a collection of potential transfers
   * with the same date
   * and the accounts don't match
   * and the amounts are the same (with opposite signs)
   * @return \Illuminate\Http\JsonResponse
   */
  public function getPotentialTransfers()
  {
    $transfers = PotentialTransfer::paginate();
    return Respond::Paginated($transfers, $transfers->all());
  }

	/**
	 * Remove the specified transfer from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
    try {
      $transfer = Transfer::where("source", $id)->orWhere("destination", $id)->firstOrFail();
      if (! $transfer->delete() ) {
        return Respond::InternalError("Failed to delete transfer with source or destination {$id}");
      }
    } catch (ModelNotFoundException $e) {
      return Respond::NotFound($e->getMessage());
    } catch (QueryException $e) {
        return Respond::QueryException($e);
    } catch (Exception $e) {
      return Respond::InternalError($e->getMessage());
    }
		return Respond::Success();
	}

}
