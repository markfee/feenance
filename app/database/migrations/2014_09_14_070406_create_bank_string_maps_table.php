<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBankStringMapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bank_string_maps', function(Blueprint $table)
		{
      $table->integer('bank_string_id')->unsigned();
      $table->integer('map_id')->unsigned();

      $table->foreign('bank_string_id')->references('id')->on('bank_strings')->onDelete('cascade')->unique();
      $table->foreign('map_id')->references('id')->on('maps')->onDelete('cascade')->unique();

      $table->primary(['bank_string_id', 'map_id']);

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
		Schema::drop('bank_string_maps');
	}

}
