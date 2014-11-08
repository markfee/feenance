<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 06/11/14
 * Time: 07:25
 */

namespace Feenance\Misc\Transformers;
use Markfee\Responder\Transformer;


class TransactionStatusTransformer extends Transformer {

  public static function transform($record) {
    return $record == null ? null : [
      "id"                => (int)$record->id,
      "name"              => $record->name,
      ];
  }
}