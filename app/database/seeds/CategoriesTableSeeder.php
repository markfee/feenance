<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;
use Feenance\MMEX\Category as mmexCategory;
use Feenance\MMEX\SubCategory as mmexSubCategory;

class CategoriesTableSeeder extends Seeder {

	public function run()
	{
    $records = mmexCategory::all();

    foreach($records as $record)
    {
      Category::create([
        "id" => $record->CATEGID,
        "name" => $record->CATEGNAME
      ]);
    }

    $records = mmexSubCategory::all();

    foreach($records as $record)
    {
      Category::create([
        "parent_id"     => $record->CATEGID,
        "name"         => $record->SUBCATEGNAME,
        "mmex_subcatid" => $record->SUBCATEGID,
      ]);
    }

	}

}