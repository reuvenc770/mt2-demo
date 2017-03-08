<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEspCampaignMappingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esp_campaign_mappings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('mappings');
			$table->timestamps();
			$table->integer('esp_id')->unsigned()->index('esp_campaign_mappings_esp_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('esp_campaign_mappings');
	}

}
