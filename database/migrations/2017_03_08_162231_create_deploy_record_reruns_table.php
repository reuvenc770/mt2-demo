<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeployRecordRerunsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('deploy_record_reruns', function(Blueprint $table)
		{
			$table->bigInteger('deploy_id')->unsigned()->default(0)->unique('deploy_id');
			$table->integer('esp_internal_id')->default(0);
			$table->integer('esp_account_id')->default(0);
			$table->boolean('delivers')->default(0);
			$table->boolean('opens')->default(0);
			$table->boolean('clicks')->default(0);
			$table->boolean('unsubs')->default(0);
			$table->boolean('complaints')->default(0);
			$table->boolean('bounces')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('deploy_record_reruns');
	}

}
