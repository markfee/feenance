<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Misc\Transformers;


class BankTransactionTransformer extends Transformer {

  public static function transform($record) {
    if ($record)
      return [
        "transaction_id"  => $record->transaction_id,
        "bank_string_id"  => $record->bank_string_id,
        "balance"         => 0.01 * $record->balance,
        "map_id"          =>  $record->bank_string ? $record->bank_string->map_id : null,
        "bank_string"     =>  $record->bank_string ? $record->bank_string->name : null,
//        "bank_string_"    => BankStringTransformer::transform($record->bank_string),
      ];
  }
}