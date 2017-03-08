<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttributionRecordTruthsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('attribution')->create('attribution_record_truths', function(Blueprint $table)
		{
			$table->bigInteger('email_id')->unsigned()->primary();
			$table->boolean('recent_import')->default(0);
			$table->boolean('has_action')->default(0)->index();
			$table->boolean('action_expired')->default(0);
			$table->boolean('additional_imports')->default(0)->index();
			$table->timestamps();
			$table->index(['recent_import','has_action','action_expired','additional_imports'], 'recent_action_expire_additional');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('attribution')->drop('attribution_record_truths');
	}

}
