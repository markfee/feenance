<?php namespace Feenance\Misc\Transformers;

use Markfee\Responder\Transformer;

class TransferTransformer extends Transformer {

  public static function transform($record) {
    return $record;
  }

  public static function transformInput($record) {
    if (!is_object($record)) {
      $record = (object) $record;
    }
    return [
      "source"      =>  (int) $record->source,
      "destination" =>  (int) $record->destination,
    ];
  }
}