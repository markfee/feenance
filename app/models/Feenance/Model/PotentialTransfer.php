<?php

namespace Feenance\models\eloquent;

class PotentialTransfer extends \Eloquent {
  protected $table = "v_potential_transfers";
  protected $fillable = [];
  protected $dates = ["date"];
  public $timestamps = false;
  static public $rules = [];

  public function save(array $options = []) {
    return false;
  }

  public static function create(array $attributes) {
    return false;
  }

}