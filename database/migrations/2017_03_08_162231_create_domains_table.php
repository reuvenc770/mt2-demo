<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDomainsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('domains', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('domain_name', 100);
			$table->string('main_site', 100);
			$table->integer('registrar_id');
			$table->integer('proxy_id')->nullable();
			$table->integer('esp_account_id');
			$table->date('created_at');
			$table->date('expires_at');
			$table->integer('doing_business_as');
			$table->boolean('domain_type');
			$table->boolean('status');
			$table->boolean('live_a_record')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('domains');
	}

}
