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
    Schema::create('units', function(Blueprint $table) {
      $table->char('id', 1);
      $table->string('unit');     // eg day, month, year
      $table->string('singular'); // eg daily, monthly, yearly
      $table->string('plural');   // eg days, months, years
      $table->primary('id');
    });

    Schema::create('standing_orders', function(Blueprint $table)
		{
      $table->increments('id');
      $table->string('name')->nullable();
      $table->datetime('previous_date');
      $table->datetime('next_date');
      $table->datetime('finish_date')->nullable();
      $table->smallInteger('increment')->unsigned();
      $table->char('unit_id');
      $table->string('exceptions')->nullable();         // eg month:february;month:march
      $table->integer('amount');            // always positive - the debit or credit account determines the sign.
      $table->boolean('next_bank_day')->default(true);  // skip to the next valid bank day
      $table->integer('account_id')->unsigned()->nullable();
      $table->integer('destination_account_id')->unsigned()->nullable();
      $table->integer('payee_id')->unsigned()->nullable();
      $table->integer('category_id')->unsigned()->nullable();
      $table->string('notes')->nullable();

      $table->timestamps();

      $table->foreign('account_id')->references('id')->on('accounts');
      $table->foreign('destination_account_id')->references('id')->on('accounts');
      $table->foreign('payee_id')->references('id')->on('payees');
      $table->foreign('category_id')->references('id')->on('categories');
      $table->foreign('unit_id')->references('id')->on('units');
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
