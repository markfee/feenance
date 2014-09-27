<?php

use Faker\Factory as Faker;
use Feenance\Model\Account;

class testAccountsTableSeeder extends Seeder {

	public function run()
	{
    $faker = Faker::create('en_GB');

//    foreach($records as $record)
    foreach(range(1, 10) as $index)
    {
      $val =
      [
//        "id"              => $record->ACCOUNTID,
        "name"            => $faker->sentence(4),
        'acc_number'      => $faker->creditCardNumber,
        'notes'           => $faker->sentence(12),
        'open'            => $faker->boolean(),
        'bank'            => $faker->creditCardType,
        'opening_balance' => $faker->numberBetween(0,3000)
      ];
      Account::create($val);
    }
	}

}