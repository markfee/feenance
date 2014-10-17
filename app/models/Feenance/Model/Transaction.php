<?php

namespace Feenance\Model;
use DB;
use Eloquent;

class Transaction extends Eloquent {
  protected $fillable = ["date", "amount", "account_id", "reconciled", "payee_id", "category_id", "notes", "bank_string_id"];
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
    }
  }

  public function balance() {
    return $this->hasOne('Feenance\Model\Balance');
  }

  public function destination() {
    // If this is the source the transfer->destination is the destination
    return $this->hasOne('Feenance\Model\Transfer', 'source');
  }

  public function source() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\Model\Transfer', 'destination');
  }

  public function bankString() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\Model\BankString', "id", "bank_string_id");
  }

  public function payee() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\Model\Payee', "id", "payee_id");
  }

  public function category() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\Model\Category', "id", "category_id");
  }


}