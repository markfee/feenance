<?php

class Map extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
      "payee_id"    => 'required'
    , "account_id"  => 'required'
    , "category_id" => 'required'
    ];

	// Don't forget to fill this array
	protected $fillable = ["payee_id", "account_id", "category_id", "transfer_id"];

  public function account()     { return $this->belongsTo("Account"); }
  public function payee()       { return $this->belongsTo("Payee"); }
  public function category()    { return $this->belongsTo("Category"); }
  public function transfer()    { return $this->belongsTo("Account"); }
}