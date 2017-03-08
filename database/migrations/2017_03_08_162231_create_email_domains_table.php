<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailDomainsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_domains', function(Blueprint $table)
		{
			$table->increments('id');
			$table->smallInteger('domain_group_id')->unsigned()->default(0)->index();
			$table->string('domain_name', 40)->default('')->unique();
			$table->boolean('is_suppressed')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('email_domains');
	}

}
