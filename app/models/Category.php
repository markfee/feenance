<?php

class Category extends \Eloquent {
  protected $fillable = ["name", "parent_id"];
  static public $rules = ["name" => "required"];

  public function parent() {
    // If this is the source the transfer->destination is the destination
    return $this->hasOne('Category', 'id', 'parent_id');
  }

  public function children() {
    // If this is the source the transfer->destination is the destination
    return $this->hasMany('Category', 'parent_id', 'id');
  }


}