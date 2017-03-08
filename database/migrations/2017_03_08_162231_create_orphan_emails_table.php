<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrphanEmailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orphan_emails', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('email_address');
			$table->boolean('missing_email_record')->default(0);
			$table->boolean('missing_email_client_instance')->default(0);
			$table->integer('esp_account_id')->unsigned()->default(0);
			$table->bigInteger('deploy_id')->unsigned();
			$table->integer('esp_internal_id')->unsigned()->default(0);
			$table->boolean('action_id')->default(0);
			$table->dateTime('datetime');
			$table->integer('adopt_attempts')->default(0);
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
		Schema::drop('orphan_emails');
	}

}
