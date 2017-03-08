<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateThirdPartyEmailStatusesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('third_party_email_statuses', function(Blueprint $table)
		{
			$table->bigInteger('email_id')->unsigned()->default(0)->primary();
			$table->enum('last_action_type', array('None','Open','Click','Conversion'))->default('None')->index('last_action_type');
			$table->integer('last_action_offer_id')->unsigned()->nullable()->default(0);
			$table->dateTime('last_action_datetime')->nullable();
			$table->integer('last_action_esp_account_id')->unsigned()->nullable()->default(0);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('third_party_email_statuses');
	}

}
