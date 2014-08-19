<?php

use MMEX\StandingOrder as mmexStandingOrder;

class StandingOrdersTableSeeder extends Seeder {

	public function run()
	{
    $records = mmexStandingOrder::all();

    foreach($records as $record)
    {
/*
BDID: "1",
ACCOUNTID: "2",
TOACCOUNTID: "-1",
PAYEEID: "1",
TRANSCODE: "Deposit",
TRANSAMOUNT: "1800",
STATUS: "",
TRANSACTIONNUMBER: "",
NOTES: "",
CATEGID: "13",
SUBCATEGID: "39",
TRANSDATE: "2014-11-01",
FOLLOWUPID: "-1",
TOTRANSAMOUNT: "1800",
REPEATS: "203",
NEXTOCCURRENCEDATE: "2015-01-01",
NUMOCCURRENCES: "-1"
*/

      $REPEATS = [
        "201" =>"+1 WEEK",
        "203" =>"+1 MONTH",
        "209" =>"+4 WEEKS",
        "207" =>"+1 YEAR",
        ];
      $amount = $record->TRANSAMOUNT;
      $credit_account = null;
      $debit_account = null;
      if ($record->TOACCOUNTID != "-1") { // transfer
        $credit_account = ($amount < 0 ? $record->ACCOUNTID : $record->TOACCOUNTID);
        $debit_account  = ($amount > 0 ? $record->ACCOUNTID : $record->TOACCOUNTID);
      } else {
        $credit_account = ($record->TRANSCODE == "Deposit"    ?  $record->ACCOUNTID : null);
        $debit_account  = ($record->TRANSCODE == "Withdrawal" ? $record->ACCOUNTID : null);
      }

      $categoryId = $record->CATEGID;
      if ($record->SUBCATEGID != -1) {
        $category = Category::where("mmex_subcatid", "=", $record->SUBCATEGID)->firstOrFail();
        $categoryId = $category->id;
      }


			Transaction::create([
        "id"                => $record->TRANSID,
        "date"              => $record->TRANSDATE,
        "amount"            => $record->TRANSAMOUNT,
        "credit_account_id" => $credit_account,
        "debit_account_id"  => $debit_account,
        "reconciled"        => ($record->STATUS == "R"),
        "payee_id"          => $record->PAYEEID == "-1" ? null : $record->PAYEEID,
        "category_id"       => $categoryId == "-1" ? null : $categoryId,
        "notes"             => $record->NOTES,
			]);
		}
	}

}