<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/08/14
 * Time: 07:40
 */

namespace Misc\Transformers;


class AccountsTransformer extends Transformer {

  private function transformAmount($account, $amount) {
    return $account ? ["acount_id" => $account, "amount" => $amount] : null;
  }

  public function transform($record) {
    return [
      "id"                => $record->id,
      "date"              => $record->date,
      "amount"            => $record->amount,
      "credit"            => $this->transformAmount($record->credit_account_id, $record->amount),
      "debit"             => $this->transformAmount($record->debit_account_id, $record->amount),
      "reconciled"        => $record->reconciled,
      "payee_id"          => $record->payee_id,
      "category_id"       => $record->category_id,
      "notes"             => $record->notes?:null ,
      ];
  }
}