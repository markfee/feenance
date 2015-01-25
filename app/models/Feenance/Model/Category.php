<?php

namespace Feenance\models\eloquent;
class Category extends \Eloquent {
  protected $fillable = ["name", "parent_id"];
  static public $rules = ["name" => "required"];

  public function parent() {
    // If this is the source the transfer->destination is the destination
    return $this->hasOne('Feenance\models\eloquent\Category', 'id', 'parent_id');
  }

  public function children() {
    // If this is the source the transfer->destination is the destination
    return $this->hasMany('Feenance\models\eloquent\Category', 'parent_id', 'id');
  }


}