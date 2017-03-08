<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListProfilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('list_profile')->create('list_profiles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 50)->default('');
			$table->boolean('admiral_only')->index();
			$table->integer('deliverable_start')->unsigned()->nullable();
			$table->integer('deliverable_end')->unsigned()->nullable();
			$table->integer('openers_start')->unsigned()->nullable();
			$table->integer('openers_end')->unsigned()->nullable();
			$table->integer('open_count')->unsigned()->nullable();
			$table->integer('clickers_start')->unsigned()->nullable();
			$table->integer('clickers_end')->unsigned()->nullable();
			$table->integer('click_count')->unsigned()->nullable();
			$table->integer('converters_start')->unsigned()->nullable();
			$table->integer('converters_end')->unsigned()->nullable();
			$table->integer('conversion_count')->unsigned()->nullable();
			$table->boolean('use_global_suppression')->default(1);
			$table->text('age_range');
			$table->text('gender');
			$table->text('zip');
			$table->text('city');
			$table->text('state');
			$table->text('device_type');
			$table->text('mobile_carrier');
			$table->boolean('insert_header')->default(0);
			$table->integer('total_count')->unsigned()->default(0);
			$table->text('device_os');
			$table->text('feeds_suppressed');
			$table->text('columns');
			$table->enum('run_frequency', array('Daily','Weekly','Monthly','Never'))->default('Daily');
			$table->timestamps();
			$table->integer('country_id');
			$table->boolean('party')->default(3);
			$table->string('ftp_folder')->default('lp');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('list_profile')->drop('list_profiles');
	}

}
