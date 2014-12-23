<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersView extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    DB::statement("
      CREATE VIEW v_transfers AS
        SELECT
          transfers.id,
          destination.date		date,
          transfers.source 		source_id,
          transfers.destination 	destination_id,
          destination.amount		amount,
          source.account_id		source_account,
          destination.account_id	destination_account
        FROM transfers
        JOIN transactions source ON transfers.source = source.id
        JOIN transactions destination ON transfers.destination = destination.id
        "
    );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    DB::statement("DROP VIEW IF EXISTS v_transfers");
	}

}
