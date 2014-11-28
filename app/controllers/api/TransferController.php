<?php namespace Feenance\Api;
use Feenance\Model\Transaction;
use Feenance\Model\Transfer;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Markfee\Responder\Respond;
use \Validator;
use \Input;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
use \Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: mark
 * Date: 26/11/14
 * Time: 07:44
 */

class TransferController extends BaseController {


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
      $query = "
      ";

    }



    }