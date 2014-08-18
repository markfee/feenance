<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function(Blueprint $table)
		{
			$table->increments('id');
      $table->datetime('date');
			$table->integer('amount')->unsigned();  // always positive - the debit or credit account determines the sign.
      $table->integer('credit_account_id')->unsigned()->nullable();
      $table->integer('debit_account_id')->unsigned()->nullable();
      $table->boolean('reconciled');
      $table->integer('payee_id')->unsigned()->nullable();
      $table->integer('category_id')->unsigned()->nullable();
			$table->string('notes')->nullable();
			$table->timestamps();

      $table->foreign('credit_account_id')->references('id')->on('accounts');
      $table->foreign('debit_account_id')->references('id')->on('accounts');
      $table->foreign('payee_id')->references('id')->on('payees');
      $table->foreign('category_id')->references('id')->on('categories');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('transactions');
	}

}
