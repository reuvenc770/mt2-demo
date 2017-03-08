<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDoingBusinessAsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('doing_business_as', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('dba_name', 100);
			$table->string('registrant_name', 100);
			$table->string('address', 100);
			$table->string('address_2', 100);
			$table->string('city', 100);
			$table->string('state', 2);
			$table->string('zip', 5);
			$table->string('dba_email', 100);
			$table->string('phone', 15);
			$table->text('po_boxes', 65535);
			$table->string('entity_name', 50);
			$table->boolean('status');
			$table->text('notes', 65535);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('doing_business_as');
	}

}
