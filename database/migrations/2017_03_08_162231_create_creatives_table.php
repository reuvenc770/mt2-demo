<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCreativesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('creatives', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('file_name')->default('');
			$table->text('creative_html');
			$table->boolean('is_approved')->default(0);
			$table->char('status', 1)->default('A');
			$table->boolean('is_original')->default(0);
			$table->boolean('trigger_flag')->default(0);
			$table->date('creative_date')->nullable();
			$table->date('inactive_date')->nullable();
			$table->string('unsub_image', 20)->nullable();
			$table->integer('default_subject')->default(0);
			$table->integer('default_from')->default(0);
			$table->string('image_directory')->nullable();
			$table->string('thumbnail')->nullable();
			$table->dateTime('date_approved')->nullable();
			$table->string('approved_by', 20)->nullable();
			$table->boolean('content_id')->default(0);
			$table->boolean('header_id')->default(0);
			$table->boolean('body_content_id')->default(0);
			$table->string('style_id', 50)->default('');
			$table->boolean('replace_flag')->default(1);
			$table->boolean('mediactivate_flag')->default(0);
			$table->boolean('hitpath_flag')->default(0);
			$table->string('comm_wizard_c3', 10)->nullable();
			$table->integer('comm_wizard_cid')->unsigned()->nullable();
			$table->integer('comm_wizard_progid')->unsigned()->nullable();
			$table->string('cr', 10)->nullable();
			$table->char('landing_page', 2)->nullable();
			$table->boolean('is_internally_approved')->default(0);
			$table->dateTime('internal_date_approved')->nullable();
			$table->string('internal_approved_by', 20)->nullable();
			$table->boolean('copywriter')->default(0);
			$table->string('copywriter_name', 5)->nullable();
			$table->text('original_html')->nullable();
			$table->integer('deleted_by')->unsigned()->default(0);
			$table->boolean('host_images')->default(1);
			$table->boolean('needs_processing')->default(0)->index('needs_processing');
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
		Schema::drop('creatives');
	}

}
