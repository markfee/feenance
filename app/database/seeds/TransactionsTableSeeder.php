<?php

use MMEX\Transaction as mmexTransaction;

class TransactionsTableSeeder extends Seeder {

	public function run()
	{
    $records = mmexTransaction::all();

    foreach($records as $record)
    {
/*
TRANSID: "124",
ACCOUNTID: "2",
TOACCOUNTID: "-1",
PAYEEID: "106",
TRANSCODE: "Withdrawal",
TRANSAMOUNT: "40",
STATUS: "R",
TRANSACTIONNUMBER: "",
NOTES: "",
CATEGID: "16",
SUBCATEGID: "47",
TRANSDATE: "2012-02-04",
FOLLOWUPID: "-1",
TOTRANSAMOUNT: "40"
*/
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
//        "id"                => $record->TRANSID,
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
//          "id"                => $record->TRANSID,
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
	}

}