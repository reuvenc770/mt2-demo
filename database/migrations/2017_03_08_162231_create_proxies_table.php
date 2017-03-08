<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProxiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('proxies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->text('ip_addresses', 65535);
			$table->string('provider_name', 100);
			$table->text('esp_account_names', 65535)->nullable();
			$table->text('isp_names', 65535)->nullable();
			$table->text('notes', 65535)->nullable();
			$table->boolean('status');
			$table->string('dba_name')->nullable();
			$table->integer('cake_affiliate_id')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('proxies');
	}

}
