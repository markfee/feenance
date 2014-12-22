<?php namespace Feenance\Model;

class Transfer extends \Eloquent {
  protected $fillable = ["source", "destination"];
  public $timestamps = false;

  static public $rules = [
    "source"      => "required|integer",
    "destination" => "required|integer",
  ];

  public function sourceTransaction() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\Model\Transaction', 'id', 'source');
  }

  public function destinationTransaction() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\Model\Transaction', 'id', 'destination');
  }

  public function sourceAccount() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\Model\Transaction', 'id', 'source')->select('id', 'account_id');
  }

  public function destinationAccount() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\Model\Transaction', 'id', 'destination')->select('id', 'account_id');
  }



}