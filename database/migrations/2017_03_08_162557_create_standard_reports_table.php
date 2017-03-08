<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStandardReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('standard_reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('external_deploy_id')->nullable()->index('deploy_id');
			$table->string('campaign_name')->default('')->unique('standard_reports_deploy_id_unique');
			$table->integer('m_deploy_id')->unsigned()->default(0);
			$table->integer('esp_account_id')->unsigned()->default(0);
			$table->integer('esp_internal_id')->default(0);
			$table->dateTime('datetime')->index('datetime');
			$table->integer('m_creative_id')->unsigned()->default(0);
			$table->integer('m_offer_id')->unsigned()->default(0);
			$table->string('name')->nullable();
			$table->string('subject')->nullable();
			$table->string('from')->nullable();
			$table->string('from_email')->nullable();
			$table->integer('m_sent')->unsigned()->nullable();
			$table->integer('e_sent')->unsigned()->nullable();
			$table->integer('delivered')->unsigned()->nullable();
			$table->integer('bounced')->unsigned()->nullable();
			$table->integer('optouts')->unsigned()->nullable();
			$table->integer('m_opens')->unsigned()->nullable();
			$table->integer('e_opens')->unsigned()->nullable();
			$table->integer('t_opens')->unsigned()->nullable();
			$table->integer('m_opens_unique')->unsigned()->nullable();
			$table->integer('e_opens_unique')->unsigned()->nullable();
			$table->integer('t_opens_unique')->unsigned()->nullable();
			$table->integer('m_clicks')->unsigned()->nullable();
			$table->integer('e_clicks')->unsigned()->nullable();
			$table->integer('t_clicks')->unsigned()->nullable();
			$table->integer('m_clicks_unique')->unsigned()->nullable();
			$table->integer('e_clicks_unique')->unsigned()->nullable();
			$table->integer('t_clicks_unique')->unsigned()->nullable();
			$table->integer('conversions')->nullable();
			$table->decimal('cost', 7)->nullable();
			$table->decimal('revenue', 7)->nullable();
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
		Schema::connection('reporting_data')->drop('standard_reports');
	}

}
