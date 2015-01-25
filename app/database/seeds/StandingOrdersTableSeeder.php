<?php

use Feenance\MMEX\StandingOrder as mmexStandingOrder;
use Feenance\models\eloquent\Category;
use Feenance\models\eloquent\Unit;

class StandingOrdersTableSeeder extends Seeder {

	public function run()
	{
    Unit::create(["id"=>"d", "unit"=>"day",    "singular"=>"daily",    "plural"=>"days"   , "days"=>1 ]   );
    Unit::create(["id"=>"w", "unit"=>"week",   "singular"=>"weekly",   "plural"=>"weeks"  , "days"=>7 ]   );
    Unit::create(["id"=>"m", "unit"=>"month",  "singular"=>"monthly",  "plural"=>"months" , "days"=>30 ]  );
    Unit::create(["id"=>"y", "unit"=>"year",   "singular"=>"yearly",   "plural"=>"years"  , "days"=>365 ] );

    $records = mmexStandingOrder::all();
    foreach($records as $record)
    {
      $REPEATS = [];
      $REPEATS["201"] = [1, "w"];
      $REPEATS["203"] = [1, "m"];
      $REPEATS["209"] = [4, "w"];
      $REPEATS["207"] = [1, "y"];

      $amount = $record->TRANSAMOUNT;
      $debit_account = null;

      if ($record->TRANSCODE == "Withdrawal" || ($record->TOACCOUNTID != "-1") ) {
        $amount *= -1;
      }
      if ($record->TOACCOUNTID != "-1") { // transfer
        $debit_account = $record->TOACCOUNTID;
      }
/*      if ($record->TOACCOUNTID != "-1") { // transfer
        $credit_account = ($amount < 0 ? $record->ACCOUNTID : $record->TOACCOUNTID);
        $debit_account  = ($amount > 0 ? $record->ACCOUNTID : $record->TOACCOUNTID);
      } else {
        $credit_account = ($record->TRANSCODE == "Deposit"    ? $record->ACCOUNTID : null);
        $debit_account  = ($record->TRANSCODE == "Withdrawal" ? $record->ACCOUNTID : null);
      }
*/

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


			\Feenance\models\eloquent\StandingOrder::create([
        "id"                => $record->BDID,
        "previous_date"     => $record->TRANSDATE,
        "next_date"         => $record->NEXTOCCURRENCEDATE,
        "increment"         => $REPEATS[$record->REPEATS][0],
        "unit_id"           => $REPEATS[$record->REPEATS][1],
        "amount"            => $amount * 100,
        "account_id"              => $record->ACCOUNTID,
        "destination_account_id"  => $record->TOACCOUNTID == "-1" ? null : $record->TOACCOUNTID,
        "payee_id"          => $record->PAYEEID == "-1" ? null : $record->PAYEEID,
        "category_id"       => $categoryId == "-1"      ? null : $categoryId,
        "notes"             => $record->NOTES,
			]);
		}
    DB::unprepared("UPDATE standing_orders SET next_date = DATE_SUB(next_date, INTERVAL 3 MONTH) WHERE unit_id <> 'y';");

    DB::unprepared(("
      UPDATE standing_orders so
	      LEFT JOIN payees payee ON payee.id = so.payee_id
	      LEFT JOIN units unit on so.unit_id = unit.id
      SET so.name =
	      CONCAT(
		      COALESCE(CONCAT(payee.name, ' '), ''),
		      '(',
			      CASE increment WHEN 1 THEN unit.singular ELSE CONCAT('every ', so.increment, ' ', unit.plural) END ,
		      ')'
	      );
    "));
	}
}