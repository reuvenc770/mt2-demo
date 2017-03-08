<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListProfileClientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('list_profile')->create('list_profile_clients', function(Blueprint $table)
		{
			$table->integer('list_profile_id')->default(0);
			$table->integer('client_id')->default(0);
			$table->index(['list_profile_id','client_id'], 'list_client');
			$table->index(['client_id','list_profile_id'], 'client_list');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('list_profile')->drop('list_profile_clients');
	}

}
