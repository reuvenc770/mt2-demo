<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSuppressionListSuppressionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('suppression')->create('suppression_list_suppressions', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->integer('suppression_list_id')->default(0);
			$table->string('email_address')->default('')->index('email_address');
			$table->string('lower_case_md5')->default('');
			$table->string('upper_case_md5')->default('');
			$table->timestamps();
			$table->index(['suppression_list_id','email_address'], 'list_email');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('suppression')->drop('suppression_list_suppressions');
	}

}
