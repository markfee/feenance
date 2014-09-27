<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStandingOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::create('increments', function(Blueprint $table) {
      $table->char('id', 1);
      $table->string('amount'); // eg day, month, year
      $table->primary('id');
    });

    Schema::create('standing_orders', function(Blueprint $table)
		{
      $table->increments('id');
      $table->datetime('previous_date');
      $table->datetime('next_date');
      $table->datetime('finish_date')->nullable();
      $table->smallInteger('increment')->unsigned();
      $table->char('increment_id');
      $table->string('exceptions')->nullable();         // eg month:february;month:march
      $table->integer('amount')->unsigned();            // always positive - the debit or credit account determines the sign.
      $table->boolean('next_bank_day')->default(true);  // skip to the next valid bank day
      $table->integer('credit_account_id')->unsigned()->nullable();
      $table->integer('debit_account_id')->unsigned()->nullable();
      $table->integer('payee_id')->unsigned()->nullable();
      $table->integer('category_id')->unsigned()->nullable();
      $table->string('notes')->nullable();

      $table->timestamps();

      $table->foreign('credit_account_id')->references('id')->on('accounts');
      $table->foreign('debit_account_id')->references('id')->on('accounts');
      $table->foreign('payee_id')->references('id')->on('payees');
      $table->foreign('category_id')->references('id')->on('categories');
      $table->foreign('increment_id')->references('id')->on('increments');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::drop('standing_orders');
    Schema::drop('increments');
	}

}
