<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountByMonthView extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    DB::statement("
      CREATE VIEW v_account_by_month AS
        select
            account_id,
            account.name as accountName,
            date_format(`transaction`.`date`,'%Y-%m') AS `month`,
            if((`transaction`.`amount` <= 0),NULL,`transaction`.`amount`) AS `credit`,
            if((`transaction`.`amount` >= 0),NULL,(-(1) * `transaction`.`amount`)) AS `debit`
        from
            transactions transaction
            left join accounts account
                on account.id = transaction.account_id
        order by date_format(`transaction`.`date`,'%Y-%m') desc
        "
    );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    DB::statement("DROP VIEW IF EXISTS v_account_by_month");
	}

}
