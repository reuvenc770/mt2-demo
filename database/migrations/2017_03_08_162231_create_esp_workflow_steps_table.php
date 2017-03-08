<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEspWorkflowStepsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esp_workflow_steps', function(Blueprint $table)
		{
			$table->integer('esp_workflow_id')->unsigned();
			$table->integer('step')->unsigned()->default(1);
			$table->integer('deploy_id')->unsigned()->index('deploy_id');
			$table->integer('offer_id')->unsigned()->nullable()->index('offer_id');
			$table->timestamps();
			$table->primary(['esp_workflow_id','step']);
			$table->index(['esp_workflow_id','deploy_id'], 'workflow_deploy');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('esp_workflow_steps');
	}

}
