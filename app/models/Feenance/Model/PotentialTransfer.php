<?php

namespace Feenance\Model;

class PotentialTransfer extends \Eloquent {
  protected $table = "v_potential_transfers";
  protected $fillable = [];
  public $timestamps = false;
  static public $rules = [];

  public function save(array $options = []) {
    return false;
  }

  public static function create(array $attributes) {
    return false;
  }

}