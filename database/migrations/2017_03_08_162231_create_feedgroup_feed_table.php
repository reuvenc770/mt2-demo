<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeedgroupFeedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feedgroup_feed', function(Blueprint $table)
		{
			$table->integer('feedgroup_id');
			$table->integer('feed_id')->index();
			$table->timestamps();
			$table->primary(['feedgroup_id','feed_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('feedgroup_feed');
	}

}
