<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRawFeedEmailFailedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('raw_feed_email_failed', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('url');
			$table->string('ip');
			$table->string('email')->index('email_index');
			$table->bigInteger('feed_id')->unsigned()->index('feed_index');
			$table->text('errors');
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
		Schema::drop('raw_feed_email_failed');
	}

}
