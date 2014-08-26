<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBalancesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('balances', function(Blueprint $table)
		{
			$table->integer('transaction_id')->unsigned();
			$table->integer('balance');
			$table->timestamps();
      $table->primary('transaction_id');
      $table->foreign('transaction_id')->references('id')->on('transactions');
		});

    DB::unprepared('
      DROP PROCEDURE IF EXISTS refresh_transaction_balances;
      CREATE PROCEDURE refresh_transaction_balances (IN in_account_id INT)
      BEGIN
        DELETE balances
          FROM balances
          INNER JOIN transactions ON balances.transaction_id = transactions.id
          AND transactions.account_id = IFNULL(in_account_id, transactions.account_id)
        ;
        INSERT balances
         SELECT
          tran.id
          , accounts.opening_balance + SUM(prior.amount) balance
          , NOW()
          , NOW()
         FROM accounts, transactions tran
          LEFT JOIN transactions prior
         ON
          prior.account_id = tran.account_id
          AND ( prior.date < tran.date
            OR (prior.date <=> tran.date AND prior.id <= tran.id)
            )
         WHERE accounts.id = IFNULL(in_account_id, accounts.id)
         AND accounts.id = tran.account_id
         GROUP BY tran.account_id, tran.date, tran.id, tran.amount
        ;
      END;
    ');


	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('balances');
	}

}
