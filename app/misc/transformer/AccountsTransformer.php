<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Feenance\Misc\Transformers;
use Markfee\Responder\Transformer;


class AccountsTransformer extends Transformer {

  private function transformAmount($account, $amount) {
    return $account ? ["acount_id" => $account, "amount" => $amount] : null;
  }

  public static function transform($record) {
    return $record ? [
      "id"                => (int)$record->id,
      "name"              => $record->name,
      "bank"              => $record->bank,
      "sort_code"         => $record->acc_number,
      "acc_number"        => $record->acc_number,
      "open"              => (boolean)$record->open,
      "opening_balance"   => 0.01 * $record->opening_balance,
      "notes"             => $record->notes?:null ,
      ] : null;
  }
}