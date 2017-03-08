<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('emails', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('email_address', 100)->default('')->unique();
			$table->integer('email_domain_id')->unsigned()->default(0)->index();
			$table->char('lower_case_md5', 32)->default('')->index('lower_case_md5');
			$table->char('upper_case_md5', 32)->default('')->index('upper_case_md5');
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
		Schema::drop('emails');
	}

}
