<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Misc\Transformers;
use \Carbon;

class TransactionTransformer extends Transformer {

  private function transformAmount($account, $amount, $transfer) {
    return $account ? ["account_id" => $account, "amount" => 0.01 * $amount, "transfer_id" => $transfer] : null;
  }

  public function transform($record) {
    return [
      "id"                => $record->id,
      "date"              => $record->date->toISO8601String(),
      "amount"            => 0.01 * $record->amount,
      "account_id"        => $record->account_id,
      "reconciled"        => $record->reconciled,
      "payee_id"          => $record->payee_id,
      "category_id"       => $record->category_id,
      "notes"             => $record->notes?:null ,
      ];
  }
}