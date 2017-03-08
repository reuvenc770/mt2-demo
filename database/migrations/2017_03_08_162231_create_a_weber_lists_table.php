<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAWeberListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('a_weber_lists', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('internal_id');
			$table->string('name');
			$table->integer('esp_account_id');
			$table->integer('total_subscribers');
			$table->string('subscribers_collection_link');
			$table->string('campaigns_collection_link');
			$table->boolean('is_active')->default(1);
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
		Schema::drop('a_weber_lists');
	}

}
