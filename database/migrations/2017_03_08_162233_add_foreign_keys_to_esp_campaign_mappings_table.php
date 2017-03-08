<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEspCampaignMappingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('esp_campaign_mappings', function(Blueprint $table)
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
		Schema::table('esp_campaign_mappings', function(Blueprint $table)
		{
			$table->dropForeign('esp_campaign_mappings_esp_id_foreign');
		});
	}

}
