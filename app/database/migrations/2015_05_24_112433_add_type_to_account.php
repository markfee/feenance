<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToAccount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('accounts', function(Blueprint $table) {
            $table->integer("account_type_id")->unsigned()->default(1);
            $table->foreign('account_type_id')->references('id')->on('account_types');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('accounts', function(Blueprint $table) {
            $table->dropForeign(['account_type_id']);
            $table->dropColumn("account_type_id");
        });
	}

}
