<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOfferPayoutsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('offer_payouts', function(Blueprint $table)
		{
			$table->integer('offer_id')->unsigned()->primary();
			$table->integer('offer_payout_type_id')->unsigned();
			$table->decimal('amount', 11, 3);
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
		Schema::drop('offer_payouts');
	}

}
