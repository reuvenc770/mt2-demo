<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeedsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feeds', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id')->unsigned()->default(0)->index('client_id');
			$table->string('name', 100)->default('')->unique('unique_name');
			$table->boolean('party')->default(3)->index('party');
			$table->string('short_name', 30)->default('');
			$table->string('password')->index('password_index');
			$table->string('host_ip')->default('52.0.242.68');
			$table->enum('status', array('Active','Paused','Inactive'))->default('Active');
			$table->decimal('revshare', 3)->nullable();
			$table->integer('vertical_id')->unsigned()->default(0);
			$table->enum('frequency', array('RT','Daily','Weekly','Monthly','TBD'))->default('TBD');
			$table->integer('type_id')->index('feed_type');
			$table->integer('country_id')->default(1)->index('country');
			$table->string('source_url')->default('');
			$table->integer('suppression_list_id')->unsigned()->nullable()->index('suppression_list_id');
			$table->timestamps();
			$table->index(['id','status'], 'feed_status');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('feeds');
	}

}
