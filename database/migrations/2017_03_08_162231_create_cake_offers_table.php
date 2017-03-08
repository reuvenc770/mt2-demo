<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCakeOffersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cake_offers', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->default(0)->primary();
			$table->string('name')->default('');
			$table->integer('vertical_id')->default(0)->index('vertical_id');
			$table->integer('cake_advertiser_id')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cake_offers');
	}

}
