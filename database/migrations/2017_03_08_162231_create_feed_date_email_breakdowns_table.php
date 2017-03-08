<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeedDateEmailBreakdownsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feed_date_email_breakdowns', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('feed_id')->default(0);
			$table->date('date')->nullable();
			$table->integer('domain_group_id')->unsigned()->default(0);
			$table->integer('total_emails');
			$table->integer('valid_emails');
			$table->integer('suppressed_emails');
			$table->integer('unique_emails');
			$table->integer('feed_duplicates');
			$table->integer('cross_feed_duplicates');
			$table->integer('phone_counts')->unsigned()->default(0);
			$table->integer('full_postal_counts')->unsigned()->default(0);
			$table->integer('bad_source_urls')->unsigned()->default(0);
			$table->integer('bad_ip_addresses')->unsigned()->default(0);
			$table->integer('other_invalid')->unsigned()->default(0);
			$table->integer('suppressed_domains')->unsigned()->default(0);
			$table->unique(['feed_id','date','domain_group_id'], 'feed_date_class');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('feed_date_email_breakdowns');
	}

}
