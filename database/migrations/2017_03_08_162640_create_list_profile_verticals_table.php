<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListProfileVerticalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('list_profile')->create('list_profile_verticals', function(Blueprint $table)
		{
			$table->integer('list_profile_id')->default(0);
			$table->integer('cake_vertical_id')->default(0);
			$table->index(['list_profile_id','cake_vertical_id'], 'list_vertical');
			$table->index(['cake_vertical_id','list_profile_id'], 'vertical_list');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('list_profile')->drop('list_profile_verticals');
	}

}
