<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserEventLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_event_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('page');
			$table->string('action');
			$table->integer('status');
			$table->timestamps();
			$table->string('ip_address');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_event_logs');
	}

}
