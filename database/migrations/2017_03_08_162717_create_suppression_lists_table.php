<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSuppressionListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('suppression')->create('suppression_lists', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('suppression_list_type')->unsigned()->default(0);
			$table->string('name')->default('0');
			$table->string('status')->default('A');
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
		Schema::connection('suppression')->drop('suppression_lists');
	}

}
