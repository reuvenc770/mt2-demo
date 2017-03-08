<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('email_actions', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('email_id')->unsigned()->default(0);
			$table->bigInteger('deploy_id');
			$table->integer('esp_account_id')->unsigned()->default(0);
			$table->integer('esp_internal_id')->unsigned()->default(0);
			$table->boolean('action_id')->default(0);
			$table->dateTime('datetime')->index();
			$table->timestamps();
			$table->unique(['email_id','deploy_id','datetime'], 'email_deploy_time');
			$table->index(['esp_internal_id','datetime'], 'campaign_date');
			$table->index(['deploy_id','datetime'], 'deploy_date');
			$table->index(['esp_internal_id','datetime'], 'esp_internal_id_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('email_actions');
	}

}
