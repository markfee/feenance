<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;
use MMEX\Category as mmexCategory;
use MMEX\SubCategory as mmexSubCategory;

class CategoriesTableSeeder extends Seeder {

	public function run()
	{
    $records = mmexCategory::all();

    foreach($records as $record)
    {
      Category::create([
        "id" => $record->CATEGID,
        "title" => $record->CATEGNAME
      ]);
    }

    $records = mmexSubCategory::all();

    foreach($records as $record)
    {
      Category::create([
        "parent_id"     => $record->CATEGID,
        "title"         => $record->SUBCATEGNAME,
        "mmex_subcatid" => $record->SUBCATEGID,
      ]);
    }

	}

}