<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContentServerStatsRawsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content_server_stats_raws', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('eid')->unsigned()->index();
			$table->bigInteger('link_id')->unsigned()->index();
			$table->integer('sub_aff_id')->unsigned()->index();
			$table->boolean('action_id')->default(0)->index();
			$table->text('user_agent', 65535);
			$table->text('referrer', 65535);
			$table->text('query_string', 65535);
			$table->dateTime('action_datetime')->index();
			$table->string('ip')->nullable()->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('content_server_stats_raws');
	}

}
