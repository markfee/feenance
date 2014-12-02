<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrimaryKeyToTransfers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::create('temp_transfers', function(Blueprint $table)
    {
      $table->engine = DB::connection()->getConfig("engine");
      // These are source and destination transactions NOT acccounts.
      // This table links two transactions together as a transfer.
      $table->increments('id');
      $table->integer('source')->unsigned();
      $table->integer('destination')->unsigned();
    });

    DB::unprepared("INSERT temp_transfers (source, destination) SELECT source, destination FROM transfers;");

    Schema::drop('transfers');

    Schema::rename("temp_transfers", "transfers");

    Schema::table('transfers', function(Blueprint $table)
    {
      $table->foreign('source')->references('id')->on('transactions')->onDelete('cascade')->unique();
      $table->foreign('destination')->references('id')->on('transactions')->onDelete('cascade')->unique();
      $table->unique(['source', 'destination']);
    });



  }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    // NO NEED TO DO ANYTHING
	}

}
