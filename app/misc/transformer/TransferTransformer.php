<?php namespace Feenance\Misc\Transformers;

use Markfee\Responder\Transformer;

class TransferTransformer extends Transformer {

  public static function transform($record) {
    return $record;
  }

  public static function transformInput($record) {
    return [
      $record->source,
      $record->destination,
    ];
  }
}