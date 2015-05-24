<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('account_types', function(Blueprint $table)
        {
            $table->engine = DB::connection()->getConfig("engine");
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_current')->default(false);
            $table->boolean('is_asset')->default(false);
            $table->boolean('is_loan')->default(false);
        });

        DB::statement('INSERT account_types (name, is_current, is_asset, is_loan)
            VALUES
            ("Current",     1, 0, 0),
            ("Savings",     1, 0, 0),
            ("Asset",       0, 1, 0),
            ("Credit Card", 0, 0, 1),
            ("Loan",        0, 0, 1),
            ("Mortgage",    0, 0, 1)
        ');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('account_types');
	}

}
