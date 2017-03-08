<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEspAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esp_accounts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('account_name')->unique('esp_accounts_account_number_unique');
			$table->integer('custom_id')->unsigned()->nullable()->unique();
			$table->string('key_1', 100);
			$table->string('key_2', 100);
			$table->integer('esp_id')->unsigned()->index('esp_accounts_esp_id_foreign');
			$table->timestamps();
			$table->boolean('status')->default(1);
			$table->boolean('enable_suppression')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('esp_accounts');
	}

}
