<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListProfileFlatTableTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('list_profile')->create('list_profile_flat_table', function(Blueprint $table)
		{
			$table->bigInteger('email_id')->unsigned()->default(0);
			$table->integer('deploy_id')->unsigned()->default(0);
			$table->smallInteger('esp_account_id')->unsigned()->default(0);
			$table->date('date')->index('date');
			$table->string('email_address', 100)->default('');
			$table->char('lower_case_md5', 32)->default('');
			$table->char('upper_case_md5', 32)->default('');
			$table->integer('email_domain_id')->unsigned()->default(0);
			$table->integer('email_domain_group_id')->unsigned()->default(0);
			$table->integer('offer_id')->unsigned()->default(0);
			$table->integer('cake_vertical_id')->unsigned()->default(0);
			$table->boolean('has_esp_open')->default(0);
			$table->boolean('has_cs_open')->default(0);
			$table->boolean('has_open')->default(0);
			$table->boolean('has_esp_click')->default(0);
			$table->boolean('has_cs_click')->default(0);
			$table->boolean('has_tracking_click')->default(0);
			$table->boolean('has_click')->default(0);
			$table->boolean('has_tracking_conversion')->default(0);
			$table->boolean('has_conversion')->default(0);
			$table->boolean('deliveries')->default(0);
			$table->smallInteger('opens')->unsigned()->default(0);
			$table->smallInteger('clicks')->unsigned()->default(0);
			$table->smallInteger('conversions')->unsigned()->default(0);
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
		Schema::connection('list_profile')->drop('list_profile_flat_table');
	}

}
