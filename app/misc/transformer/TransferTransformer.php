<?php namespace Feenance\Misc\Transformers;

use Markfee\Responder\Transformer;

class TransferTransformer extends Transformer {

  public static function transform($record) {
    return [
      "id"                      => (int) $record->id,
      "date"                    => $record->date->toISO8601String(),
      "source_id"               => (int) $record->source_id,
      "destination_id"          => (int) $record->destination_id,
      "amount"                  => 0.01 * $record->amount,
      "source_account_id"       => (int) $record->source_account,
      "destination_account_id"  => (int) $record->destination_account,
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