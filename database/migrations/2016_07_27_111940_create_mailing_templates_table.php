<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailingTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string("template_name");
            $table->tinyInteger("template_type");
            $table->text("template_html");
            $table->text(("template_text"));
        });
        Schema::create('esp_account_mailing_templates', function (Blueprint $table) {
            $table->integer('esp_account_id');
            $table->integer('mailing_template_id');

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
        Schema::drop('esp_account_mailing_templates');
    }
}
