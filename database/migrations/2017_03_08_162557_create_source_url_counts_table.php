<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSourceUrlCountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('source_url_counts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('feed_id')->unsigned()->index();
			$table->string('source_url')->index();
			$table->integer('count')->unsigned()->default(0);
			$table->date('capture_date')->index();
			$table->unique(['feed_id','source_url','capture_date'], 'unique_count_index');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('source_url_counts');
	}

}
