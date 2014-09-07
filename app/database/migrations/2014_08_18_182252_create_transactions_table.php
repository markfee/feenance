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

    if (!App::runningUnitTests()) {
      DB::unprepared('
        CREATE TRIGGER transaction_update_balance AFTER UPDATE ON transactions FOR EACH ROW
        BEGIN
          IF @disable_transaction_triggers IS NULL THEN
            IF NOT (NEW.account_id <=> OLD.account_id) THEN
              CALL refresh_balances_for_account(OLD.account_id);
              CALL refresh_balances_for_account(NEW.account_id);
            ELSEIF NOT (NEW.amount <=> OLD.amount) OR NOT (NEW.date <=> OLD.date) THEN
              SET @date = LEAST(NEW.date, OLD.date);
              CALL refresh_balances_from_date(OLD.account_id, @date);
            END IF;
          END IF;
        END;
      ');

      DB::unprepared('
        CREATE TRIGGER transaction_insert_balance AFTER INSERT ON transactions FOR EACH ROW
        BEGIN
          IF @disable_transaction_triggers IS NULL THEN
            CALL refresh_balances_from_date(NEW.account_id, NEW.date);
          END IF;
        END;
      ');

      DB::unprepared('
        CREATE TRIGGER transaction_delete_balance AFTER DELETE ON transactions FOR EACH ROW
        BEGIN
          IF @disable_transaction_triggers IS NULL THEN
            CALL refresh_balances_from_date(OLD.account_id, OLD.date);
          END IF;
        END;
      ');



      /*

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
