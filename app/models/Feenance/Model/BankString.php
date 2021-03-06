<?php

namespace Feenance\Model;
class BankString extends \Eloquent {

  // Add your validation rules here
  public static $rules = [
    // 'name' => 'required'
  ];

  // Don't forget to fill this array
  protected $fillable = ["account_id", "name"];

  public static function findOrCreate($account_id, $name) {
    // TODO - UNCOMMENT THESE PRINTS AND CREATE A LOG
    $bank_string = BankString::where("name", "=", "{$name}")->where("account_id", "=", $account_id);
    if ($bank_string->count() > 0) {
//      print "FOUND:    ({$account_id}, {$name})";
      return $bank_string;
    }
//    print "\nCREATING: (id: ??? account_id: {$account_id}, name: {$name})";
    $newRecord = BankString::create(["account_id" => $account_id, "name" => $name]);
//    print "\nCREATED:  (id: {$newRecord->id} account_id: {$newRecord->account_id}, name: {$newRecord->name})";
    if ($newRecord->name != $name)
      dd($newRecord);
    return BankString::where("id", "=", "{$newRecord->id}");
  }

  public function bankTransaction() {
    return $this->hasMany('Feenance\Model\BankTransaction');
  }

  public function payee() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\Model\Payee', "id", "payee_id");
  }

  public function category() {
    // If this is the destination the transfer->source is the source
    return $this->hasOne('Feenance\Model\Category', "id", "category_id");
  }

}