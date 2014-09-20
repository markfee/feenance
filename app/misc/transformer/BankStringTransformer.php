<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Misc\Transformers;


class BankStringTransformer extends Transformer {

  public static function transform($record) {
    if ($record)
      return [
        "id"                => (int)$record->id,
        "account_id"        => (int)$record->account_id,
        "name"              => $record->name,
        ];
  }
}