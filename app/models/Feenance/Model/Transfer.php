<?php namespace Feenance\Model;

class Transfer extends \Eloquent {
  protected $fillable = ["source", "destination"];
  public $timestamps = false;

  static public $rules = [
    "source"      => "required|integer",
    "destination" => "required|integer",
  ];


}