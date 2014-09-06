<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Misc\Transformers;


class CategoryTransformer extends Transformer {

  public function transform($record) {
    return [
      "id"            => (int)$record->id,
      "fullName"      => $record->parent_id ? $record->parent->name . ": " . $record->name : $record->name,
      "name"          => $record->name,
      "parent_id"     => $record->parent_id ? (int)$record->parent_id : null
      ];
  }
}