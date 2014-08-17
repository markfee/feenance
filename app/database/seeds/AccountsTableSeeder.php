<?php

use MMEX\Accounts as mmexAccount;

class AccountsTableSeeder extends Seeder {

	public function run()
	{
    $records = mmexAccount::all();

    foreach($records as $record)
    {
/*
ACCOUNTID: "1",
ACCOUNTNAME: "Smile Joint",
ACCOUNTTYPE: "Checking",
ACCOUNTNUM: "01266869",
STATUS: "Closed",
NOTES: "",
HELDAT: "Smile",
WEBSITE: "www.smile.co.uk",
CONTACTINFO: "",
ACCESSINFO: "",
INITIALBAL: "0",
FAVORITEACCT: "TRUE",
CURRENCYID: "6"
},
*/


      Account::create([
        "id"              => $record->ACCOUNTID,
        "title"           => $record->ACCOUNTNAME,
        'acc_number'      => $record->ACCOUNTNUM ?:null,
        'notes'           => $record->NOTES ?:null,
        'open'            => ($record->STATUS != "Closed"),
        'bank'            => $record->HELDAT ?:null,
        'opening_balance' => $record->INITIALBAL
      ]);
    }
	}

}