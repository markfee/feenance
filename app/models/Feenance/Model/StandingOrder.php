<?php

namespace Feenance\Model;
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
    return $this->hasOne('Feenance\Model\Payee', "id", "payee_id");
  }

  public function category() {
    return $this->hasOne('Feenance\Model\Category', "id", "category_id");
  }

  public function incrementUnit() {
    return $this->hasOne('Feenance\Model\Increment', "id", "increment_id");
  }

  public function account() {
    return $this->hasOne('Feenance\Model\Account', "id", "account_id");
  }

  public function destination() {
    return $this->hasOne('Feenance\Model\Account', "id", "destination_account_id");
  }


}