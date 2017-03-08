<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEspAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('esp_accounts', function(Blueprint $table)
		{
			$table->foreign('esp_id')->references('id')->on('esps')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('esp_accounts', function(Blueprint $table)
		{
			$table->dropForeign('esp_accounts_esp_id_foreign');
		});
	}

}
