<?php

use MMEX\Transaction as mmexTransaction;

class TransactionsTableSeeder extends Seeder {

	public function run()
	{
    $records = mmexTransaction::all();

    foreach($records as $record)
    {
      $amount = $record->TRANSAMOUNT;
      $credit_account = null;
      $debit_account = null;
      if ($record->TRANSCODE == "Withdrawal" || ($record->TOACCOUNTID != "-1") ) {
        $amount *= -1;
      }

      $categoryId = $record->CATEGID;
      if ($record->SUBCATEGID != -1) {
        $category = Category::where("mmex_subcatid", "=", $record->SUBCATEGID)->firstOrFail();
        $categoryId = $category->id;
      }

      Transaction::create([
        "date"              => $record->TRANSDATE,
        "amount"            => $amount * 100,
        "account_id"        => $record->ACCOUNTID,
        "reconciled"        => ($record->STATUS == "R"),
        "payee_id"          => $record->PAYEEID == "-1" ? null : $record->PAYEEID,
        "category_id"       => $categoryId == "-1"      ? null : $categoryId,
        "notes"             => $record->NOTES,
      ]);
      if ($record->TOACCOUNTID != "-1") { // transfer
        Transaction::create([
          "date"              => $record->TRANSDATE,
          "amount"            => $amount * -100,
          "account_id"        => $record->TOACCOUNTID,
          "reconciled"        => ($record->STATUS == "R"),
          "payee_id"          => $record->PAYEEID == "-1" ? null : $record->PAYEEID,
          "category_id"       => $categoryId == "-1"      ? null : $categoryId,
          "notes"             => $record->NOTES,
        ]);
      }
		}
    DB::unprepared('call refresh_balances();');
	}

}