<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDomainGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('domain_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 20)->default('')->unique();
			$table->boolean('priority')->default(1);
			$table->enum('status', array('Active','Paused'));
			$table->enum('country', array('US','UK'))->default('US');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('domain_groups');
	}

}
