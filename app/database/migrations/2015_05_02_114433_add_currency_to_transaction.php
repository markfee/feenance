<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrencyToTransaction extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('transactions', function(Blueprint $table) {
            $table->char("currency_code", 12)->default("GBP_penny");
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('transactions', function(Blueprint $table) {
            $table->dropColumn("currency_code");
        });
	}

}
