<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOfferFromMapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('offer_from_maps', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('offer_id')->default(0);
			$table->integer('from_id')->default(0)->index('from_id');
			$table->unique(['offer_id','from_id'], 'offer_from');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('offer_from_maps');
	}

}
