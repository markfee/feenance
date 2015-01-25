<?php namespace Feenance\models\eloquent;

class TransferView extends \Eloquent {
  public $timestamps = false;
  protected $table = "v_transfers";
  protected $fillable = [];
  protected $dates = ["date"];

  public function save(array $options = []) {
    return false;
  }

  public static function create(array $attributes) {
    return false;
  }

}