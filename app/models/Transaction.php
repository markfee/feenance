<?php

class Transaction extends Eloquent {
  protected $fillable = [
    "date", "amount", "account_id", "reconciled", "payee_id", "category_id", "notes"
  ];
  protected $dates = ["date"];
  static public $rules = [
    "date"        => "required|date"
  , "amount"      => "required|integer|not_in:0"
  , "account_id"  => "required|integer"
  ];

/*
 * id: "1085",
date: "2014-12-27T00:00:00+0000",
amount: -90,
account_id: "2",
balance: 3754.66,
reconciled: "0",
payee_id: "84",
category_id: "64",
notes: null,
source: null,
destination: null*/



  public function balance() {
    return $this->hasOne('Balance');
  }

  public function destination() {
    // If this is the source the transfer->destination is the destination
    return $this->hasOne('Transfer', 'source');
  }

  public function source() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Transfer', 'destination');
  }


}