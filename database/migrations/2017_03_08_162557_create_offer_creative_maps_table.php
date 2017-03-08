<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOfferCreativeMapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('offer_creative_maps', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('offer_id')->default(0);
			$table->integer('creative_id')->default(0)->index('creative_id');
			$table->unique(['offer_id','creative_id'], 'offer_creative');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('offer_creative_maps');
	}

}
