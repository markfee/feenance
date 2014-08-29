<?php

class Transaction extends \Eloquent {
  protected $fillable = [];
  protected $dates = ["date"];

  public function balance() {
    return $this->hasOne('Balance');
  }

}