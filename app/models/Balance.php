<?php

class Balance extends \Eloquent {
	protected $fillable = [];

  public function transaction() {
    return $this->belongsTo('Transaction');
  }

}