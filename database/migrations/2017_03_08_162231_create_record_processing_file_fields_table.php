<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecordProcessingFileFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('record_processing_file_fields', function(Blueprint $table)
		{
			$table->integer('feed_id')->primary();
			$table->boolean('email_index');
			$table->boolean('source_url_index');
			$table->boolean('capture_date_index');
			$table->boolean('ip_index');
			$table->boolean('first_name_index')->nullable();
			$table->boolean('last_name_index')->nullable();
			$table->boolean('address_index')->nullable();
			$table->boolean('address2_index')->nullable();
			$table->boolean('city_index')->nullable();
			$table->boolean('state_index')->nullable();
			$table->boolean('zip_index')->nullable();
			$table->boolean('country_index')->nullable();
			$table->boolean('gender_index')->nullable();
			$table->boolean('phone_index')->nullable();
			$table->boolean('dob_index')->nullable();
			$table->text('other_field_index')->nullable();
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
		Schema::drop('record_processing_file_fields');
	}

}
