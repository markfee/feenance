<?php namespace Feenance\models\eloquent;

class Account extends \Eloquent {

  protected $fillable = ["name", "opening_balance", "sort_code", "acc_number", "notes", "bank", "open"];

  static public $rules = ["name" => "required"];

}