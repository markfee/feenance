<?php namespace Feenance\models\eloquent;

class BankString extends \Eloquent {

  // Add your validation rules here
  public static $rules = [
    // 'name' => 'required'
  ];

  // Don't forget to fill this array
  protected $fillable = ["account_id", "name", "category_id", "payee_id"];

  public static function findOrCreate($account_id, $name) {
    $bank_string = BankString::where("name", "=", "{$name}")->where("account_id", "=", $account_id);
    if ($bank_string->count() > 0) {
      return $bank_string;
    }

    $newRecord = BankString::create(["account_id" => $account_id, "name" => $name]);
    return BankString::where("id", "=", "{$newRecord->id}");
  }

  public function bankTransaction() {
    return $this->hasMany('Feenance\models\eloquent\BankTransaction');
  }

  public function payee() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\models\eloquent\Payee', "id", "payee_id");
  }

  public function category() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\models\eloquent\Category', "id", "category_id");
  }

}