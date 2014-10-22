<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

    $this->call('CategoriesTableSeeder');
    $this->call('PayeesTableSeeder');
    $this->call('AccountsTableSeeder');
    $this->call('TransactionsTableSeeder');
    $this->call('StandingOrdersTableSeeder');

		$this->call('BankStringsTableSeeder');
	}

}
