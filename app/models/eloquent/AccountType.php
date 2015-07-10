<?php namespace Feenance\models\eloquent;

class AccountType extends \Eloquent {
    public $timestamps = false;
    protected $fillable = ["name", "is_current", "is_asset", "is_loan"];
}