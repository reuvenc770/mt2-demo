<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecordDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('record_data', function(Blueprint $table)
		{
			$table->bigInteger('email_id')->unsigned()->default(0)->primary();
			$table->boolean('is_deliverable')->default(1);
			$table->string('first_name')->default('');
			$table->string('last_name')->default('');
			$table->string('address')->default('');
			$table->string('address2')->default('');
			$table->string('city')->default('')->index();
			$table->string('state')->default('')->index();
			$table->string('zip')->default('')->index();
			$table->string('country')->default('');
			$table->enum('gender', array('M','F','UNK'))->default('UNK')->index();
			$table->string('ip', 16);
			$table->string('phone');
			$table->string('source_url')->default('');
			$table->date('dob')->nullable();
			$table->string('device_type')->default('')->index();
			$table->string('device_name')->default('')->index();
			$table->string('carrier')->default('')->index();
			$table->date('capture_date');
			$table->date('subscribe_date');
			$table->integer('last_action_offer_id')->nullable();
			$table->date('last_action_date')->nullable()->index('last_action_date');
			$table->text('other_fields')->nullable();
			$table->timestamps();
			$table->index(['email_id','is_deliverable','subscribe_date'], 'email_status_date');
			$table->index(['last_action_offer_id','last_action_date'], 'last_action');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('record_data');
	}

}
