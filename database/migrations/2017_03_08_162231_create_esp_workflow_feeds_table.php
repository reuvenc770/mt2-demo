<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEspWorkflowFeedsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esp_workflow_feeds', function(Blueprint $table)
		{
			$table->integer('esp_workflow_id')->unsigned();
			$table->integer('feed_id')->unsigned()->index('feed_id');
			$table->primary(['esp_workflow_id','feed_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('esp_workflow_feeds');
	}

}
