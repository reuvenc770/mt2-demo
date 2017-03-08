<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeploysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('deploys', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('deploy_name', 100);
			$table->date('send_date');
			$table->integer('esp_account_id');
			$table->string('external_deploy_id', 100);
			$table->integer('offer_id');
			$table->integer('creative_id');
			$table->integer('from_id');
			$table->integer('subject_id');
			$table->integer('template_id');
			$table->integer('mailing_domain_id');
			$table->integer('content_domain_id');
			$table->integer('list_profile_combine_id');
			$table->integer('cake_affiliate_id');
			$table->boolean('encrypt_cake')->default(0);
			$table->boolean('fully_encrypt')->default(0);
			$table->string('url_format', 20)->default('short');
			$table->text('notes', 65535);
			$table->boolean('deployment_status');
			$table->timestamps();
			$table->integer('user_id');
			$table->boolean('party')->default(3);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('deploys');
	}

}
