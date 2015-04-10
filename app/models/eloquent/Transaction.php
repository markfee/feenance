<?php namespace Feenance\models\eloquent;

use DB;
use Eloquent;

class Transaction extends Eloquent {
  protected $fillable = ["date", "amount", "account_id", "reconciled", "payee_id", "category_id", "notes", "bank_string_id", "batch_id"];
  protected $dates = ["date"];
  static public $rules = [
    "date" => "required|date"
    , "amount" => "required|integer|not_in:0"
    , "account_id" => "required|integer"
  ];

  public static function startBulk() {
    if (DB::connection()->getDriverName() == "sqlite")
      return; // This won't work and isn't necessary with SQLITE
    DB::unprepared('SET @disable_transaction_triggers = 1;');
  }

  public static function finishBulk($refresh = false) {
    if (DB::connection()->getDriverName() == "sqlite")
      return; // This won't work and isn't necessary with SQLITE
    DB::unprepared('SET @disable_transaction_triggers = NULL;');
    if ($refresh) {
      DB::unprepared('call refresh_balances();');
      Transaction::autoReconcile();
    }
  }

  /**
   * Set Reconciled flag where balance matches bank balance.
   * TODO: this function should also unreconcile reconciled transactions that don't match
   */
  public static function autoReconcile() {
    DB::unprepared("
      UPDATE transactions JOIN balances
      ON balances.transaction_id = transactions.id AND transactions.bank_balance = balances.balance
      SET transactions.reconciled = 1,
       transactions.status_id = 2
       WHERE transactions.reconciled = 0 OR transactions.status_id != 2;"
    );
  }

  public function balance() {
    return $this->hasOne('Feenance\models\eloquent\Balance');
  }

  public function destination() {
    // If this is the source the transfer->destination is the destination
    return $this->hasOne('Feenance\models\eloquent\Transfer', 'source');
  }

  public function source() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\models\eloquent\Transfer', 'destination');
  }

  public function bankString() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\models\eloquent\BankString', "id", "bank_string_id");
  }

  public function payee() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\models\eloquent\Payee', "id", "payee_id");
  }

  public function category() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\models\eloquent\Category', "id", "category_id");
  }

  public function status() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\models\eloquent\TransactionStatus', "id", "status_id");
  }

}