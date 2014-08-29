<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransfersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transfers', function(Blueprint $table)
		{
			$table->integer('source')->unsigned();
			$table->integer('destination')->unsigned();
      $table->foreign('source')->references('id')->on('transactions')->onDelete('cascade')->unique();
      $table->foreign('destination')->references('id')->on('transactions')->onDelete('cascade')->unique();
      $table->primary(['source', 'destination']);
//      $table->index('source')->unique();
//      $table->index('dest')->unique();

    });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('transfers');
	}

}
