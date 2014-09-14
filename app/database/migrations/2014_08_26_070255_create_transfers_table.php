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
      // These are source and destination transactions NOT acccounts.
      // This table links two transactions together as a transfer.
			$table->integer('source')->unsigned();
			$table->integer('destination')->unsigned();
      $table->foreign('source')->references('id')->on('transactions')->onDelete('cascade')->unique();
      $table->foreign('destination')->references('id')->on('transactions')->onDelete('cascade')->unique();
      $table->primary(['source', 'destination']);

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
