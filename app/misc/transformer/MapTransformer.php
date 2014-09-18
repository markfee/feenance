<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Misc\Transformers;


class MapTransformer extends Transformer {

  public static function transform($record) {
    return [
      "id"                => (int)  $record->id,
      "account_id"        => (int)  $record->account_id,
      "payee_id"          => (int)  $record->payee_id,
      "category_id"       => (int)  $record->category_id,
      "transfer_id"    => $record->transfer_id? (int)  $record->transfer_id: null,
      "account"        => AccountsTransformer::transform($record->account),
      "payee"          => PayeeTransformer::transform($record->payee),
      "category"       => CategoryTransformer::transform($record->category),
      "transfer"       => AccountsTransformer::transform($record->transfer),

      ];
  }
}