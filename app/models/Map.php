<?php

class Map extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'name' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ["payee_id", "account_id", "category_id"];

}