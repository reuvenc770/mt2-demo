<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOfferSubjectMapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('offer_subject_maps', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('offer_id')->default(0);
			$table->integer('subject_id')->default(0)->index('subject_id');
			$table->unique(['offer_id','subject_id'], 'offer_subject');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('offer_subject_maps');
	}

}
