<?php namespace Feenance\Misc\Transformers;

use Markfee\Responder\Transformer;
use \Carbon\Carbon;

class PotentialTransferTransformer extends Transformer {

  public static function transform($record) {
    return [
      /* RAW
       * date: "2011-09-27 06:44:52",
       * source_id: "1791",
       * destination_id: "16",
       * source_account_id: "13",
       * destination_account_id: "2",
       * source_amount: "-20000",
       * destination_amount: "20000"
       */
      "date"                    => $record->date->toISO8601String(),
      "amount"                  => 0.01 * $record->destination_amount,
      "source"                  => (int) $record->source_id,
      "destination"             => (int) $record->destination_id,
      "source_account_id"       => (int) $record->source_account_id,
      "destination_account_id"  => (int) $record->destination_account_id,
    ];
  }

  public static function transformInput($record) {
    return [
      $record->source,
      $record->destination,
    ];
  }
}