<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailAttributableFeedLatestDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_attributable_feed_latest_data', function(Blueprint $table)
		{
			$table->bigInteger('email_id')->unsigned()->default(0);
			$table->integer('feed_id')->unsigned()->default(0);
			$table->date('subscribe_date');
			$table->date('capture_date');
			$table->enum('attribution_status', array('POR','POA','MOA','ATTR'))->default('POR');
			$table->string('first_name')->default('');
			$table->string('last_name')->default('');
			$table->string('address')->default('');
			$table->string('address2')->default('');
			$table->string('city')->default('');
			$table->string('state')->default('');
			$table->string('zip')->default('');
			$table->string('country')->default('');
			$table->enum('gender', array('M','F','UNK'))->default('UNK');
			$table->string('ip', 16);
			$table->string('phone');
			$table->string('source_url')->default('');
			$table->date('dob')->nullable();
			$table->string('device_type')->default('');
			$table->string('device_name')->default('');
			$table->string('carrier')->default('');
			$table->text('other_fields')->nullable();
			$table->timestamps();
			$table->primary(['email_id','feed_id']);
			$table->index(['feed_id','updated_at'], 'feed_updated');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('email_attributable_feed_latest_data');
	}

}
