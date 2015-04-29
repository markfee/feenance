<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultCategoryToAccount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('accounts', function(Blueprint $table) {
            $table->integer("category_id")->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
        });	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('accounts', function(Blueprint $table) {
            $table->dropColumn("category_id");
        });
	}

}
