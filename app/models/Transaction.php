<?php

class Transaction extends \Eloquent {
  protected $fillable = [];
  protected $dates = ["date"];

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