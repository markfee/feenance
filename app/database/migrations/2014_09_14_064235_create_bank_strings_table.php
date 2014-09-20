<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBankStringsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bank_strings', function(Blueprint $table)
		{
			$table->increments('id');
      $table->integer('account_id')->unsigned();
      $table->string('name');
			$table->timestamps();

      $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
      $table->index('name')->unique();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bank_strings');
	}

}
