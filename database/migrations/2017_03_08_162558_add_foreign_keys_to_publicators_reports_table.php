<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPublicatorsReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->table('publicators_reports', function(Blueprint $table)
		{
			$table->foreign('esp_account_id')->references('id')->on('esp_accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->table('publicators_reports', function(Blueprint $table)
		{
			$table->dropForeign('publicators_reports_esp_account_id_foreign');
		});
	}

}
