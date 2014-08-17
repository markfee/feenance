<?php

use MMEX\Payee as mmexPayee;

class PayeesTableSeeder extends Seeder {

	public function run()
	{
    $records = mmexPayee::all();

    foreach($records as $record)
		{
      $categoryId = $record->CATEGID;
      if ($record->SUBCATEGID != -1) {
        $category = Category::where("mmex_subcatid", "=", $record->SUBCATEGID)->firstOrFail();
        $categoryId = $category->id;
      }
			Payee::create([
        "id" => $record->PAYEEID,
        "title" => $record->PAYEENAME,
        "category_id" => $categoryId
			]);
		}
	}

}