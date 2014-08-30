<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Misc\Transformers;


class PayeeTransformer extends Transformer {

  public function transform($record) {
    return [
      "id"                => (int)$record->id,
      "name"              => $record->title,
      "category_id"       => (int)$record->category_id,
      ];
  }
}