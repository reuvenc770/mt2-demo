<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttributionFeedReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('attribution')->create('attribution_feed_reports', function(Blueprint $table)
		{
			$table->integer('feed_id')->index();
			$table->decimal('cpc_revenue', 11, 4)->unsigned()->default(0.0000);
			$table->decimal('cpc_revshare', 11, 4)->unsigned()->default(0.0000);
			$table->decimal('cpa_revenue', 11, 4)->unsigned()->default(0.0000);
			$table->decimal('cpa_revshare', 11, 4)->unsigned()->default(0.0000);
			$table->decimal('cpm_revenue', 11, 4)->unsigned()->default(0.0000);
			$table->decimal('cpm_revshare', 11, 4)->unsigned()->default(0.0000);
			$table->integer('uniques')->unsigned()->default(0);
			$table->date('date')->index();
			$table->timestamps();
			$table->unique(['feed_id','date'], 'feed_date_unique');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('attribution')->drop('attribution_feed_reports');
	}

}
