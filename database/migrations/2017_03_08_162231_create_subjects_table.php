<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subjects', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('subject_line')->default('');
			$table->boolean('is_approved')->default(0);
			$table->char('status', 1)->default('A');
			$table->boolean('is_original')->default(0);
			$table->dateTime('date_approved')->nullable();
			$table->string('approved_by', 30)->nullable();
			$table->date('inactive_date')->nullable()->index('inactive_date');
			$table->boolean('internal_approved_flag')->default(0);
			$table->dateTime('internal_date_approved')->nullable();
			$table->string('internal_approved_by', 30)->nullable();
			$table->boolean('copywriter')->default(0);
			$table->string('copywriter_name', 5)->nullable();
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
		Schema::drop('subjects');
	}

}
