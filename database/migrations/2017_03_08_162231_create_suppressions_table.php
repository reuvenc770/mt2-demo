<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSuppressionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('suppressions', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('email_address', 150)->index();
			$table->integer('type_id');
			$table->integer('esp_account_id');
			$table->integer('esp_internal_id');
			$table->date('date');
			$table->timestamps();
			$table->integer('reason_id');
			$table->index(['email_address','reason_id']);
			$table->index(['esp_internal_id','esp_account_id','email_address']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('suppressions');
	}

}
