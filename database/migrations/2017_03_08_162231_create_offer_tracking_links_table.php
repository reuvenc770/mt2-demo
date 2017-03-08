<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOfferTrackingLinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('offer_tracking_links', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('offer_id')->default(0);
			$table->integer('link_num')->default(1);
			$table->integer('link_id')->default(0)->index('link_id');
			$table->string('url', 500)->default('');
			$table->string('approved_by', 30)->nullable();
			$table->date('date_approved')->nullable();
			$table->timestamps();
			$table->unique(['offer_id','link_num'], 'offer_link');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('offer_tracking_links');
	}

}
