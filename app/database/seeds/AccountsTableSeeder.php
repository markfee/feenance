<?php

use Feenance\MMEX\Accounts as mmexAccount;
use Feenance\models\eloquent\Account;

class AccountsTableSeeder extends Seeder {

	public function run()
	{
    $records = mmexAccount::all();

    foreach($records as $record)
    {
      Account::create([
        "id"              => $record->ACCOUNTID,
        "name"           => $record->ACCOUNTNAME,
        'acc_number'      => $record->ACCOUNTNUM ?:null,
        'notes'           => $record->NOTES ?:null,
        'open'            => ($record->STATUS != "Closed"),
        'bank'            => $record->HELDAT ?:null,
        'opening_balance' => $record->INITIALBAL * 100
      ]);
    }
	}

}