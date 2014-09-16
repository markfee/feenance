<?php

class BankTransaction extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
    "transaction_id"  => "required",
    "bank_string_id"  => "required",
    "balance"          => "required",
  ];

	protected $fillable = [
    "transaction_id",
    "bank_string_id",
    "balance"
  ];

  public function transaction() {
    return $this->belongsTo('Transaction');
  }

  public function bank_string() {
    return $this->belongsTo('BankString');
  }

}