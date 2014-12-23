<?php namespace Feenance\Misc\Transformers;

use Markfee\Responder\Transformer;

class TransferTransformer extends Transformer {

  public static function transform($record) {
    return [
      "id"                      =>  (int) $record->id,
      "source"                  =>  (int) $record->source,
      "source_transaction"      =>  static::transformTransaction($record->sourceTransaction),
      "destination"             =>  (int) $record->destination,
      "destination_transaction" =>  static::transformTransaction($record->destinationTransaction),
    ];
  }

  public static function transformTransaction($record) {
    return [
      "date"              => $record->date->toISO8601String(),
      "amount"            => 0.01 * $record->amount,
      "account_id"        => $record->account_id,
      "reconciled"        => $record->reconciled,
    ];
  }

  public static function transformInput($record) {
    if (!is_object($record)) {
      $record = (object) $record;
    }
    return [
      "source"      =>  (int) $record->source,
      "destination" =>  (int) $record->destination,
    ];
  }
}