<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAWeberEmailActionsStoragesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('a_weber_email_actions_storages', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('email_id')->unsigned()->default(0)->index();
			$table->integer('esp_account_id')->unsigned()->default(0);
			$table->integer('esp_internal_id')->default(0);
			$table->integer('deploy_id')->unsigned()->default(0);
			$table->boolean('action_id')->default(0);
			$table->dateTime('datetime');
			$table->timestamps();
			$table->unique(['email_id','deploy_id','action_id','datetime'], 'email_deploy_action_time');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('a_weber_email_actions_storages');
	}

}
