<?php

namespace Feenance\Model;
class Balance extends \Eloquent {
  protected $fillable = [];

  public function transaction() {
    return $this->belongsTo('Feenance\Model\Transaction');
  }

}