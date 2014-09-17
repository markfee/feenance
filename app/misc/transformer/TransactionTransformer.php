<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Misc\Transformers;
use \Carbon\Carbon;
use Mockery\CountValidator\Exception;

class TransactionTransformer extends Transformer {

  private function transformAmount($account, $amount, $transfer) {
    return $account ? ["account_id" => $account, "amount" => 0.01 * $amount, "transfer_id" => $transfer] : null;
  }

  public static function transform($record) {
    return [
      "id"                => $record->id,
      "date"              => $record->date->toISO8601String(),
      "amount"            => 0.01 * $record->amount,
      "account_id"        => $record->account_id,
      "balance"           => $record->balance ? 0.01 * $record->balance->balance : null,
      "reconciled"        => $record->reconciled,
      "payee_id"          => $record->payee_id,
      "category_id"       => $record->category_id,
      "notes"             => $record->notes?:null ,
      "source"            => $record->source?$record->source->source:null ,
      "destination"       => $record->destination?$record->destination->destination:null ,
/*
      "bank_balance"      => $record->bank_transaction ? 0.01 * $record->bank_transaction->balance : null,
      "bank_string"       => $record->bank_transaction ? $record->bank_transaction->bank_string : null,
      "bank_string_id"    => $record->bank_transaction ? $record->bank_transaction->bank_string_id : null,
*/
      "bank_transaction"            => BankTransactionTransformer::transform($record->bankTransaction),

      ];
  }

  public static function transformInput($record) {
    if (isset($record["amount"])) $record["amount"] *= 100;
    if (isset($record["date"])) {
      $record["date"] = substr($record["date"], 0, 10);
    }
    return $record;
  }
}