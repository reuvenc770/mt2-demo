<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMailingTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mailing_templates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('template_name');
			$table->boolean('template_type');
			$table->text('template_html', 65535);
			$table->text('template_text', 65535);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mailing_templates');
	}

}
