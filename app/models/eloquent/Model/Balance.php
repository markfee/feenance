<?php

namespace Feenance\models\eloquent;
class Balance extends \Eloquent {
  protected $fillable = [];

  public function transaction() {
    return $this->belongsTo('Feenance\models\eloquent\Transaction');
  }

}