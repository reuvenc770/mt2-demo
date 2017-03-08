<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clients', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->default('')->unique('unique_name');
			$table->string('address', 100)->default('');
			$table->string('address2', 100)->default('');
			$table->string('city', 50)->default('');
			$table->string('state', 15)->default('');
			$table->string('zip', 10)->default('');
			$table->string('email_address', 100)->default('');
			$table->string('phone', 50)->default('');
			$table->enum('status', array('Active','Paused','Inactive'))->default('Active');
			$table->decimal('revshare', 3)->default(0.15);
			$table->timestamps();
			$table->index(['id','status'], 'client_status');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('clients');
	}

}
