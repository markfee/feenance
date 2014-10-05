<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Feenance\Misc\Transformers;
use Markfee\Responder\Transformer;


class PayeeTransformer extends Transformer {

  public static function transform($record) {
    return $record == null ? null : [
      "id"                => (int)$record->id,
      "name"              => $record->name,
      "category_id"       => $record->category_id ? (int)$record->category_id : null,
      ];
  }
}