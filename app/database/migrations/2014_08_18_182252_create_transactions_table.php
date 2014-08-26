<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function(Blueprint $table)
		{
			$table->increments('id');
      $table->datetime('date');
      $table->integer('amount');      // in pence, signed.
      $table->integer('account_id')->unsigned();
//      $table->integer('debit_account_id')->unsigned()->nullable();
      $table->boolean('reconciled');
      $table->integer('payee_id')->unsigned()->nullable();
      $table->integer('category_id')->unsigned()->nullable();
			$table->string('notes')->nullable();
			$table->timestamps();

      $table->foreign('account_id')->references('id')->on('accounts');
      $table->foreign('payee_id')->references('id')->on('payees');
      $table->foreign('category_id')->references('id')->on('categories');
      $table->index(['date', 'id']);
		});
/*

$str = '    UPDATE transactions SET balance = (SELECT SUM(totals.amount) FROM transactions totals WHERE totals.account_id = transactions.account_id AND totals.date <=   transactions.date|
  AND
  (
    totals.date <   transactions.date
    OR (totals.date <=> transactions.date AND totals.id <= transactions.id)
                  )
            )
';

    DB::unprepared('
      CREATE TRIGGER transaction_update_balance BEFORE UPDATE ON transactions FOR EACH ROW
      BEGIN
        IF NOT (NEW.amount <=> OLD.amount) THEN
          SET @diff = NEW.amount - OLD.amount;
          SET NEW.balance = OLD.balance + @diff;
          UPDATE transactions SET balance = balance + @diff
            WHERE account_id = NEW.account_id
            AND (
                    transactions.date > NEW.date
                OR (transactions.date <=> NEW.date AND transactions.id > NEW.id)
                );
        END IF;
      END;
    ');
    DB::unprepared('
      CREATE TRIGGER transaction_insert_balance BEFORE INSERT ON transactions FOR EACH ROW
      BEGIN
        SET NEW.balance = NEW.amount + IFNULL(
        (SELECT SUM(transactions.amount) FROM transactions
          WHERE account_id = NEW.account_id
                AND
                (
                    transactions.date < NEW.date
                OR (transactions.date <=> NEW.date AND transactions.id < NEW.id)
                )
          ), 0)
          ;
      END;
    ');*/
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
