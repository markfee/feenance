<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionStatusesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::dropIfExists('transaction_statuses');
		Schema::create('transaction_statuses', function(Blueprint $table)
		{
			$table->increments('id');
      $table->string('name')->nullable();
      $table->char('code', 4);
			$table->timestamps();
		});
    DB::unprepared("INSERT transaction_statuses VALUES(1, 'Unreconciled',             'U',    Now(), Now());");
    DB::unprepared("INSERT transaction_statuses VALUES(2, 'Reconciled',               'R',    Now(), Now());");
    DB::unprepared("INSERT transaction_statuses VALUES(3, 'Expected standing order',  'ESO',  Now(), Now());");
    DB::unprepared("INSERT transaction_statuses VALUES(4, 'Planned definite',         'PD',   Now(), Now());");
    DB::unprepared("INSERT transaction_statuses VALUES(5, 'Planned possible',         'PP',   Now(), Now());");

    Schema::table('transactions', function(Blueprint $table)
    {
      $table->integer('status_id')->default(1)->unsigned();
      $table->foreign('status_id')->references('id')->on('transaction_statuses');
    });

    Schema::table('transactions', function(Blueprint $table)
    {
      $table->integer('status_id')->unsigned();
    });

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::drop('transaction_statuses');
	}

}
