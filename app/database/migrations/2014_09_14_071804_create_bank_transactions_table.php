<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBankTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
   * This table holds extra details imported from the bank transaction, such as the banks balance (used for reconciliation)
   * and the text used on the bank transaction (used to map to payees / categories).
	 */
	public function up()
	{
		Schema::create('bank_transactions', function(Blueprint $table)
		{
      $table->integer('transaction_id')->unsigned();
      $table->integer('bank_string_id')->unsigned();
      $table->integer('balance');

      $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade')->nullable();
      $table->foreign('bank_string_id')->references('id')->on('bank_strings')->onDelete('cascade');

			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bank_transactions');
	}

}
