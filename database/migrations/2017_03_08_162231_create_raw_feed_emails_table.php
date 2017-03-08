<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRawFeedEmailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('raw_feed_emails', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->integer('feed_id')->unsigned()->default(0);
			$table->string('email_address')->index('email_index');
			$table->string('source_url');
			$table->dateTime('capture_date');
			$table->string('ip');
			$table->string('first_name')->nullable()->default('');
			$table->string('last_name')->nullable()->default('');
			$table->string('address')->nullable()->default('');
			$table->string('address2')->nullable()->default('');
			$table->string('city')->nullable()->default('');
			$table->string('state')->nullable()->default('');
			$table->string('zip')->nullable()->default('');
			$table->string('country')->nullable()->default('');
			$table->string('gender')->nullable()->default('');
			$table->string('phone')->nullable()->default('');
			$table->date('dob')->nullable();
			$table->text('other_fields');
			$table->timestamps();
			$table->index(['feed_id','email_address'], 'feed_email_index');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('raw_feed_emails');
	}

}
