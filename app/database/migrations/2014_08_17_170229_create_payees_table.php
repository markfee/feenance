<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePayeesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payees', function(Blueprint $table)
		{
      $table->engine = DB::connection()->getConfig("engine");
      $table->increments('id');
			$table->string('name');
			$table->integer('category_id')->nullable()->unsigned();
      $table->foreign('category_id')->references('id')->on('categories');
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
		Schema::drop('payees');
	}

}
