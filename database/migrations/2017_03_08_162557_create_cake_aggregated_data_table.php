<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCakeAggregatedDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('cake_aggregated_data', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('subid_1', 30)->default('');
			$table->string('subid_2', 100)->default('0');
			$table->bigInteger('email_id')->default(0);
			$table->string('subid_4', 100)->default('');
			$table->string('subid_5', 100)->default('');
			$table->integer('affiliate_id')->unsigned()->default(0);
			$table->string('user_agent_string', 500)->default('');
			$table->integer('clicks')->default(0);
			$table->integer('conversions')->default(0);
			$table->decimal('revenue', 7)->default(0.00);
			$table->date('clickDate')->nullable()->default('0000-00-00')->index('clickDate');
			$table->date('campaignDate')->default('0000-00-00')->index('campaignDate');
			$table->timestamps();
			$table->unique(['subid_1','subid_2'], 's1_s2');
			$table->index(['subid_1','email_id'], 'campaign_email');
			$table->index(['email_id','subid_1','clickDate'], 'actions_join');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('cake_aggregated_data');
	}

}
