<?php

use Feenance\MMEX\Transaction as mmexTransaction;
use Feenance\Model\Category;
use Feenance\Model\Transfer;

class TransactionsTableSeeder extends Seeder {

	public function run()
	{
    $records = mmexTransaction::all();

    DB::unprepared('SET @disable_transaction_triggers = 1;');
    foreach($records as $record)
    {

      if ($record->STATUS != "R")
        continue;
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
//      print "\nrecord->ACCOUNTID: {$record->ACCOUNTID}";
      $src = \Feenance\Model\Transaction::create([
        "date"              => $record->TRANSDATE,
        "amount"            => $amount * 100,
        "account_id"        => $record->ACCOUNTID,
        "reconciled"        => ($record->STATUS == "R"),
        "payee_id"          => $record->PAYEEID == "-1" ? null : $record->PAYEEID,
        "category_id"       => $categoryId == "-1"      ? null : $categoryId,
        "notes"             => $record->NOTES,
      ]);

      if ($record->TOACCOUNTID != "-1") { // transfer
//        print "\nrecord->TOACCOUNTID : {$record->ACCOUNTID}";
        $destination = \Feenance\Model\Transaction::create([
          "date"              => $record->TRANSDATE,
          "amount"            => $amount * -100,
          "account_id"        => $record->TOACCOUNTID,
          "reconciled"        => ($record->STATUS == "R"),
          "payee_id"          => $record->PAYEEID == "-1" ? null : $record->PAYEEID,
          "category_id"       => $categoryId == "-1"      ? null : $categoryId,
          "notes"             => $record->NOTES,
        ]);
        Transfer::create([
          'source'      => $src->id,
          'destination' => $destination->id
        ]);
      }
		}
    DB::unprepared('SET @disable_transaction_triggers = NULL;');
    DB::unprepared('call refresh_balances();');
	}

}