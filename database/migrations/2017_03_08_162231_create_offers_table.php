<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOffersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('offers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->default('');
			$table->boolean('is_approved')->default(0);
			$table->char('status', 1)->default('I');
			$table->integer('advertiser_id');
			$table->boolean('offer_payout_type_id')->default(1);
			$table->string('unsub_link')->default('');
			$table->char('exclude_days', 7)->default('NNNNNNN');
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
		Schema::drop('offers');
	}

}
