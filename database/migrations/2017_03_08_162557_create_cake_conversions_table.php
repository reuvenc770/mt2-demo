<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCakeConversionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('cake_conversions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('email_id')->default('0');
			$table->string('s1')->default('')->index();
			$table->string('s2')->default('');
			$table->string('s3')->default('');
			$table->string('s4')->default('')->index();
			$table->string('s5')->default('');
			$table->string('click_id');
			$table->dateTime('conversion_date')->nullable()->index();
			$table->string('conversion_id')->default('0');
			$table->boolean('is_click_conversion')->default(0);
			$table->integer('request_session_id');
			$table->integer('affiliate_id');
			$table->integer('offer_id');
			$table->integer('advertiser_id');
			$table->integer('campaign_id')->default(0);
			$table->integer('creative_id')->default(0);
			$table->decimal('received_raw', 7, 4)->default(0.0000);
			$table->decimal('received_usa', 7, 4)->default(0.0000);
			$table->decimal('paid_raw', 7, 4)->default(0.0000);
			$table->decimal('paid_usa', 7, 4)->default(0.0000);
			$table->boolean('paid_currency_id')->default(1);
			$table->boolean('received_currency_id')->default(1);
			$table->decimal('conversion_rate', 7, 4)->default(0.0000);
			$table->string('ip')->nullable();
			$table->timestamps();
			$table->unique(['click_id','conversion_id'], 'unique_conversion');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('cake_conversions');
	}

}
