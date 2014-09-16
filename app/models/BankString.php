<?php

class BankString extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'name' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ["account_id", "name"];

  public static function findOrCreate($account_id, $name) {
    if ($bankString = BankString::where("name", "=", "{$name}")->where("account_id", "=", $account_id)->first()) {
      return $bankString;
    }
    return BankString::create( [ "account_id" => $account_id, "name" => $name ] );
  }

  public function map() {
    return $this->hasOne('Map', "id");
  }

  public function bankTransaction() {
    return $this->hasMany('BankTransaction');
  }

}