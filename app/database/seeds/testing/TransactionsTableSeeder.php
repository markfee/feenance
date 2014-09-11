<?php

use Faker\Factory as Faker;
use Carbon\Carbon;

class testTransactionsTableSeeder extends Seeder {

  public function run()
  {
    $faker = Faker::create('en_GB');
    $accounts=Account::all();
    $date = Carbon::now();
    $date->subMonths(60);
    $balance = 0;
    $accountBalances = [];
    foreach(range(1, 10) as $index) {
      $amount = $faker->numberBetween(-250000, 250000);
      $account = $accounts->random(1)->id;
      if (empty($accountBalances[$account])) $accountBalances[$account] = 0;
      $accountBalances[$account] += $amount;

      $src = Transaction::create([
        "date"              =>  $date,
        "amount"            =>  $amount,
        "account_id"        =>  $account,
        "reconciled"        =>  $faker->boolean(),
        "payee_id"          =>  null,
        "category_id"       =>  null,
        "notes"             =>  $faker->sentence(),
      ]);
      Balance::create([
        "transaction_id"  =>$src->id,
        "balance"         =>$accountBalances[$account]
      ]);
      if ($src->amount > 0 && $faker->boolean()) {
        $account = $accounts->random(1)->id;
        if (empty($accountBalances[$account])) $accountBalances[$account] = 0;
        $accountBalances[$account] -= $amount;

        $destination = Transaction::create([
          "date"              => $date,
          "amount"            => -$amount,
          "account_id"        => $account,
          "reconciled"        => $src->reconciled,
          "payee_id"          => $src->payeeId,
          "category_id"       => $src->categoryId,
          "notes"             => $faker->sentence(),
        ]);
        Transfer::create([
          'source'      => $src->id,
          'destination' => $destination->id
          ]);
        Balance::create([
          "transaction_id"  =>$destination->id,
          "balance"         =>$accountBalances[$account]
        ]);

      }
      $date->addDays($faker->numberBetween(1, 365));
    }
/*    $rec=Transaction::all();
    dd($rec);*/
  }

}