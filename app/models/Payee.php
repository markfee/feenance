<?php

class Payee extends \Eloquent {
  protected $fillable = ["name", "category_id"];
  static public $rules = ["name" => "required"];
}