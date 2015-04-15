<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionView extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $this->down();
        DB::statement("
            CREATE VIEW v_transactions AS
            SELECT
                transaction.id,
                date_format(transaction.date,'%Y-%m-%d') AS date,
                transaction.batch_id,
                transaction.account_id,
                transaction.payee_id,
                transaction.category_id,
                if((transaction.amount <= 0),NULL,(+0.01 * transaction.amount)) AS `credit`,
                if((transaction.amount >= 0),NULL,(-0.01 * transaction.amount)) AS `debit`,
                0.01 * transaction.amount movement,
                0.01 * balance.balance balance
            FROM
                transactions transaction
            LEFT JOIN balances balance
                ON transaction.id = balance.transaction_id
            ORDER BY transaction.date DESC;
        ");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement("DROP VIEW IF EXISTS v_transactions");
	}

}
