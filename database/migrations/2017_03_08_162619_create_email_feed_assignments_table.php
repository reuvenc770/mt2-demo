<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailFeedAssignmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('attribution')->create('email_feed_assignments', function(Blueprint $table)
		{
			$table->bigInteger('email_id')->unsigned()->primary();
			$table->integer('feed_id')->unsigned()->index('email_client_assignments_client_id_index');
			$table->timestamps();
			$table->date('capture_date');
			$table->index(['email_id','feed_id'], 'email_client_assignments_email_id_client_id_index');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('attribution')->drop('email_feed_assignments');
	}

}
