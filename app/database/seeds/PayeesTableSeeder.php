<?php

use Feenance\MMEX\Payee as mmexPayee;
use Feenance\models\eloquent\Category;

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
			\Feenance\models\eloquent\Payee::create([
        "id" => $record->PAYEEID,
        "name" => $record->PAYEENAME,
        "category_id" => $categoryId
			]);
		}
	}

}