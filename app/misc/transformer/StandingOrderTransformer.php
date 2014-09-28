<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 27/09/14
 * Time: 06:36
 */

namespace Feenance\Misc\Transformers;


class StandingOrderTransformer extends Transformer {

  public static function transform($record) {
    return [
      "id"                => (int) $record->id,
      "name"              => $record->name,
      "previous_date"     => $record->previous_date->toISO8601String(),
      "next_date"         => $record->next_date->toISO8601String(),
      "finish_date"       => $record->finish_date ? $record->finish_date->toISO8601String() : null,
      "increment"         => (int) $record->increment,
      "increment_unit"    => $record->incrementUnit,
      "exceptions"        => $record->exceptions,
      "amount"            => 0.01 * $record->amount,
      "skip_to_bank_day"  => (boolean)$record->next_bank_day,

      "account_id"        => $record->account_id,
      "destination_id"        => $record->destination_account_id,

      "account"    => AccountsTransformer::transform($record->account),
      "destination"     => AccountsTransformer::transform($record->destination),

      "notes"             => $record->notes?:null ,
      "payee"             => $record->payee_id ?    PayeeTransformer::transform($record->payee) : null,
//      "category"          => $record->category,
      "category"          => $record->category_id ? CategoryTransformer::transform($record->category) : null,

    ];
  }
}