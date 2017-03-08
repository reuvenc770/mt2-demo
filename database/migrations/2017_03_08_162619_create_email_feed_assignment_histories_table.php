<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailFeedAssignmentHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('attribution')->create('email_feed_assignment_histories', function(Blueprint $table)
		{
			$table->integer('email_id')->unsigned()->index('email_client_assignment_histories_email_id_index');
			$table->integer('prev_feed_id')->unsigned();
			$table->integer('new_feed_id')->unsigned();
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
		Schema::connection('attribution')->drop('email_feed_assignment_histories');
	}

}
