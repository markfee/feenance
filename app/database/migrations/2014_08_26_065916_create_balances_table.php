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
      $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
		});

    if (!App::runningUnitTests()) {
      DB::unprepared('

        DROP PROCEDURE IF EXISTS refresh_balances;
        DROP PROCEDURE IF EXISTS refresh_balances_for_account;
        DROP PROCEDURE IF EXISTS refresh_balances_from_date;

        CREATE PROCEDURE refresh_balances ()
        BEGIN
          CALL refresh_balances_from_date(NULL, NULL);
        END;

        CREATE PROCEDURE refresh_balances_for_account (IN in_account_id INT)
        BEGIN
          CALL refresh_balances_from_date(in_account_id, NULL);
        END;

        CREATE PROCEDURE refresh_balances_from_date (IN in_account_id INT, IN in_from_date DATETIME)
        BEGIN
          DELETE balances
            FROM balances
            INNER JOIN transactions ON balances.transaction_id = transactions.id
            AND transactions.account_id = IFNULL(in_account_id, transactions.account_id)
            AND (in_from_date IS NULL OR transactions.date >= in_from_date)
          ;
          INSERT balances
           SELECT
            tran.id
            , accounts.opening_balance + SUM(prior.amount) balance
            , NOW()
            , NOW()
           FROM
              accounts
           ,  transactions tran
           LEFT JOIN transactions prior
              ON    prior.account_id = tran.account_id
              AND ( prior.date < tran.date
                OR (prior.date <=> tran.date AND prior.id <= tran.id)
              )
           LEFT JOIN balances balance_exists
            ON tran.id = balance_exists.transaction_id
           WHERE  balance_exists.transaction_id IS NULL
           AND    accounts.id = IFNULL(in_account_id, accounts.id)
           AND    accounts.id = tran.account_id
           GROUP BY tran.account_id, tran.date, tran.id, tran.amount
          ;
        END;
      ');
    }
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    DB::unprepared('DROP PROCEDURE IF EXISTS refresh_transaction_balances;');
		Schema::drop('balances');
	}

}
