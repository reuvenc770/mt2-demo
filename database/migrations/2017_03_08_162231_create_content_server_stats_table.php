<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContentServerStatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content_server_stats', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('email_id')->unsigned()->default(0);
			$table->bigInteger('deploy_id')->unsigned()->default(0);
			$table->integer('action_id')->unsigned()->default(0);
			$table->dateTime('datetime')->index('datetime');
			$table->timestamps();
			$table->unique(['email_id','deploy_id','action_id','datetime'], 'email_deploy_action_date');
			$table->index(['deploy_id','datetime'], 'deploy_datetime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('content_server_stats');
	}

}
