<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListProfileOffersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('list_profile')->create('list_profile_offers', function(Blueprint $table)
		{
			$table->integer('list_profile_id')->default(0);
			$table->integer('offer_id')->default(0);
			$table->index(['list_profile_id','offer_id'], 'list_offer');
			$table->index(['offer_id','list_profile_id'], 'offer_list');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('list_profile')->drop('list_profile_offers');
	}

}
