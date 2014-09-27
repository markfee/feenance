<?php

class StandingOrder extends \Eloquent {
  protected $fillable = [];
  protected $dates = ['previous_date', 'next_date', 'finish_date', 'created_at', 'updated_at'];
  /*
    id: "1",
    previous_date: "2014-11-01 00:00:00",
    next_date: "2015-01-01 00:00:00",
    finish_date: null,
    increment: "1",
    increment_id: "m",
    exceptions: null,
    amount: "1800",
    next_bank_day: "1",
    credit_account_id: "2",
    debit_account_id: null,
    payee_id: "1",
    category_id: "51",
    notes: "",
    created_at: "2014-09-20 08:58:37",
    updated_at: "2014-09-20 08:58:37"
*/


  public function payee() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Payee', "id", "payee_id");
  }

  public function category() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Category', "id", "category_id");
  }

  public function incrementUnit() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Increment', "id", "increment_id");
  }


}