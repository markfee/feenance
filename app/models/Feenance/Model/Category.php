<?php

namespace Feenance\Model;
class Category extends \Eloquent {
  protected $fillable = ["name", "parent_id"];
  static public $rules = ["name" => "required"];

  public function parent() {
    // If this is the source the transfer->destination is the destination
    return $this->hasOne('Feenance\Model\Category', 'id', 'parent_id');
  }

  public function children() {
    // If this is the source the transfer->destination is the destination
    return $this->hasMany('Feenance\Model\Category', 'parent_id', 'id');
  }


}