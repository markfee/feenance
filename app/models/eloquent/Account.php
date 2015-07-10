<?php namespace Feenance\models\eloquent;

class Account extends \Eloquent {

  protected $fillable = ["name", "opening_balance", "sort_code", "acc_number", "notes", "bank", "open", "category_id", "account_type_id"];

  static public $rules = ["name" => "required"];

}