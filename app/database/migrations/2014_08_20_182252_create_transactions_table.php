<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->engine = DB::connection()->getConfig("engine");
            $table->increments('id');
            $table->datetime('date');
            $table->integer('amount');      // in pence, signed.
            $table->integer('account_id')->unsigned();
            $table->boolean('reconciled');
            $table->integer('standing_order_id')->unsigned()->nullable();
            $table->integer('payee_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('notes')->nullable();

            $table->integer('bank_string_id')->unsigned()->nullable();
            $table->integer('bank_balance')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('payee_id')->references('id')->on('payees');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('standing_order_id')->references('id')->on('standing_orders');
            $table->foreign('bank_string_id')->references('id')->on('bank_strings');
            $table->index(['date', 'id']);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transactions');
    }

}
