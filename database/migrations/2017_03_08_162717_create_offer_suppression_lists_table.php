<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOfferSuppressionListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('suppression')->create('offer_suppression_lists', function(Blueprint $table)
		{
			$table->integer('offer_id')->unsigned()->default(0);
			$table->integer('suppression_list_id')->unsigned()->default(0)->index('suppression_list_id');
			$table->primary(['offer_id','suppression_list_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('suppression')->drop('offer_suppression_lists');
	}

}
