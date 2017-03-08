<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListProfileCombinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('list_profile')->create('list_profile_combines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->integer('list_profile_id')->nullable();
			$table->timestamps();
			$table->boolean('party')->default(3);
			$table->string('ftp_folder')->default('lp_combines');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('list_profile')->drop('list_profile_combines');
	}

}
