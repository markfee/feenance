<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Feenance\Misc\Transformers;
use Markfee\Responder\Transformer;


class BankStringTransformer extends Transformer {

  public static function transform($record) {
    if ($record)
      return [
        "id"                => (int)$record->id,
        "account_id"        => (int)$record->account_id,
        "name"              => $record->name,
        "payee"             => $record->payee_id ? PayeeTransformer::transform($record->payee) : null,
        "category"          => $record->category_id ? CategoryTransformer::transform($record->category) : null,
        ];
  }
}