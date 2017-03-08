<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListProfileFeedsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('list_profile')->create('list_profile_feeds', function(Blueprint $table)
		{
			$table->integer('list_profile_id')->default(0);
			$table->integer('feed_id')->default(0);
			$table->index(['list_profile_id','feed_id'], 'list_feed');
			$table->index(['feed_id','list_profile_id'], 'feed_list');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('list_profile')->drop('list_profile_feeds');
	}

}
