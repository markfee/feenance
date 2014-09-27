<?php

use Feenance\MMEX\StandingOrder as mmexStandingOrder;
use Feenance\Model\Category;
use Feenance\Model\Increment;

class StandingOrdersTableSeeder extends Seeder {

	public function run()
	{
    Increment::create(["id"=>"d", "amount"=>"Days"]);
    Increment::create(["id"=>"w", "amount"=>"Weeks"]);
    Increment::create(["id"=>"m", "amount"=>"Months"]);
    Increment::create(["id"=>"y", "amount"=>"Years"]);

    $records = mmexStandingOrder::all();
    foreach($records as $record)
    {
      $REPEATS = [];
      $REPEATS["201"] = [1, "w"];
      $REPEATS["203"] = [1, "m"];
      $REPEATS["209"] = [4, "w"];
      $REPEATS["207"] = [1, "y"];

      $amount = $record->TRANSAMOUNT;
      $credit_account = null;
      $debit_account = null;
      if ($record->TOACCOUNTID != "-1") { // transfer
        $credit_account = ($amount < 0 ? $record->ACCOUNTID : $record->TOACCOUNTID);
        $debit_account  = ($amount > 0 ? $record->ACCOUNTID : $record->TOACCOUNTID);
      } else {
        $credit_account = ($record->TRANSCODE == "Deposit"    ? $record->ACCOUNTID : null);
        $debit_account  = ($record->TRANSCODE == "Withdrawal" ? $record->ACCOUNTID : null);
      }

      $categoryId = $record->CATEGID;
      try{
        if ($record->SUBCATEGID != "-1") {
          $category = Category::where("mmex_subcatid", "=", $record->SUBCATEGID)->firstOrFail();
          $categoryId = $category->id;
        }
      } catch(Exception $ex) {
      }
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


			\Feenance\Model\StandingOrder::create([
        "id"                => $record->BDID,
        "previous_date"     => $record->TRANSDATE,
        "next_date"         => $record->NEXTOCCURRENCEDATE,
        "increment"         => $REPEATS[$record->REPEATS][0],
        "increment_id"      => $REPEATS[$record->REPEATS][1],
        "amount"            => $record->TRANSAMOUNT,
        "credit_account_id" => $credit_account,
        "debit_account_id"  => $debit_account,
        "payee_id"          => $record->PAYEEID == "-1" ? null : $record->PAYEEID,
        "category_id"       => $categoryId == "-1"      ? null : $categoryId,
        "notes"             => $record->NOTES,
			]);
		}
	}

}