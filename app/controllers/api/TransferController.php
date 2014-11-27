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

/**
 * Created by PhpStorm.
 * User: mark
 * Date: 26/11/14
 * Time: 07:44
 */

class TransferController extends BaseController {


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
      $sourceTransaction = Transaction::findOrFail($sourceId);
      $destinationTransaction = Transaction::findOrFail($destinationId);
    } catch(ModelNotFoundException $ex) {
      return Respond::NotFound("Transaction not found for transfer");
    }

    try {
      if ($sourceTransaction->account_id == $destinationTransaction->account_id) {
        return Respond::ValidationFailed("Source and Destination accounts must be different");
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

} 