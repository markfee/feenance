<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 27/09/14
 * Time: 06:36
 */

namespace Misc\Transformers;


class StandingOrderTransformer extends Transformer {
  public static function transform($record) {
    return $record;
    return [
      "id"                => (int)$record->id,
      "name"              => $record->name,
      "category_id"       => $record->category_id ? (int)$record->category_id : null,
    ];
  }
}