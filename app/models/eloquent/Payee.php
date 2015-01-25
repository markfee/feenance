<?php namespace Feenance\models\eloquent;

class Payee extends \Eloquent {
  protected $fillable = ["name", "category_id"];
  static public $rules = ["name" => "required"];
}