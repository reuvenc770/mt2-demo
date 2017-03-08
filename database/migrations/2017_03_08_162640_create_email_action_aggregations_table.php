<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailActionAggregationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('list_profile')->create('email_action_aggregations', function(Blueprint $table)
		{
			$table->bigInteger('email_id')->unsigned()->default(0);
			$table->integer('deploy_id')->unsigned()->default(0);
			$table->date('date')->index('date');
			$table->integer('deliveries')->unsigned()->default(0);
			$table->integer('opens')->unsigned()->default(0);
			$table->integer('clicks')->unsigned()->default(0);
			$table->integer('conversions')->unsigned()->default(0);
			$table->timestamps();
			$table->unique(['email_id','deploy_id','date'], 'email_deploy_date');
			$table->index(['email_id','date'], 'email_date');
			$table->index(['deploy_id','date'], 'deploy_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('list_profile')->drop('email_action_aggregations');
	}

}
