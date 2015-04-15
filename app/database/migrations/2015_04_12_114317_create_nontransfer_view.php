<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNontransferView extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $this->down();
        DB::statement("
            CREATE VIEW v_non_transfers AS
            SELECT v_transactions.*
                FROM v_transactions
                LEFT JOIN transfers
                    ON (    transfers.source        = v_transactions.id
                        OR  transfers.destination   = v_transactions.id
                        )
            WHERE transfers.id IS NULL
        ");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement("DROP VIEW IF EXISTS v_non_transfers");
	}

}
