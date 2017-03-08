<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailFeedInstancesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_feed_instances', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('email_id')->unsigned()->default(0);
			$table->integer('feed_id')->unsigned()->default(0);
			$table->date('subscribe_datetime')->nullable();
			$table->date('unsubscribe_datetime')->nullable();
			$table->enum('status', array('A','B','C','U'));
			$table->string('first_name', 20)->default('');
			$table->string('last_name', 40)->default('');
			$table->string('address', 50)->default('');
			$table->string('address2', 50)->default('');
			$table->string('city', 50)->default('');
			$table->char('state', 2);
			$table->string('zip', 10)->default('');
			$table->char('country')->default('');
			$table->date('dob')->nullable();
			$table->enum('gender', array('','M','F'))->default('');
			$table->string('phone', 15)->default('');
			$table->string('mobile_phone', 15)->default('');
			$table->string('work_phone', 15)->default('');
			$table->date('capture_date')->index('email_client_instances_capture_date_index');
			$table->string('source_url', 50)->default('');
			$table->string('ip', 15)->default('0.0.0.0');
			$table->timestamps();
			$table->index(['email_id','feed_id'], 'email_client_instances_email_id_client_id_index');
			$table->index(['feed_id','email_id'], 'email_client_instances_client_id_email_id_index');
			$table->index(['email_id','capture_date'], 'email_client_instances_email_id_capture_date_index');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('email_feed_instances');
	}

}
