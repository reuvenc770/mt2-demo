<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRegistrarsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('registrars', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->string('username');
			$table->string('last_cc', 4);
			$table->string('contact_credit_card');
			$table->boolean('status');
			$table->text('dba_names', 65535);
			$table->string('password', 100);
			$table->text('notes', 65535);
			$table->string('other_last_cc', 4)->nullable();
			$table->string('other_contact_credit_card', 100)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('registrars');
	}

}
