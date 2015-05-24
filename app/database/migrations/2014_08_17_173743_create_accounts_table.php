<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->engine = DB::connection()->getConfig("engine");
            $table->increments('id');
            $table->string('name');
            $table->string('acc_number')->nullable();
            $table->string('sort_code')->nullable();
            $table->string('notes')->nullable();
            $table->boolean('open')->default(true);
            $table->string('bank')->nullable();
            $table->integer('opening_balance')->default(0);
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
        Schema::drop('accounts');
    }

}
