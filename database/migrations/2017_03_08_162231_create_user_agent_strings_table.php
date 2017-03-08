<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAgentStringsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_agent_strings', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('user_agent_string', 500)->default('')->unique('user_agent_string');
			$table->string('browser', 50)->default('');
			$table->string('device', 50)->default('Misc')->index('device');
			$table->boolean('is_mobile')->default(0);
			$table->index(['is_mobile','device'], 'mobile_device');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_agent_strings');
	}

}
