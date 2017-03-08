<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCakeEncryptedLinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cake_encrypted_links', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('affiliate_id')->default(0);
			$table->integer('creative_id')->default(0);
			$table->string('encrypted_hash')->default('');
			$table->timestamps();
			$table->unique(['affiliate_id','creative_id'], 'affiliate_creative');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cake_encrypted_links');
	}

}
