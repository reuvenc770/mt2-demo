<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateYmlpCampaignsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('ymlp_campaigns', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('esp_account_id')->unsigned()->default(0);
			$table->date('date');
			$table->string('sub_id');
			$table->timestamps();
			$table->index(['esp_account_id','date']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('ymlp_campaigns');
	}

}
