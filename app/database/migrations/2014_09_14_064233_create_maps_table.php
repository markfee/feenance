<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('maps', function(Blueprint $table)
		{
			$table->increments('id');
      $table->integer('payee_id')->unsigned();
      $table->integer('category_id')->unsigned();
      $table->integer('account_id')->unsigned();
      $table->integer('transfer_id')->unsigned()->nullable();

      $table->foreign('payee_id')->references('id')->on('payees')->onDelete('cascade');
      $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
      $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
      $table->foreign('transfer_id')->references('id')->on('accounts')->onDelete('cascade');


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
		Schema::drop('maps');
	}

}
