<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeploysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deploys', function (Blueprint $table) {
            $table->increments('id');
            $table->date("send_date");
            $table->integer("esp_account_id");
            $table->integer("list_profile_id");
            $table->integer("offer_id");
            $table->integer("creative_id");
            $table->integer("creative_from_id");
            $table->integer("creative_subject_id");
            $table->integer("creative_template_id");
            $table->integer("mailing_domain_id");
            $table->integer("content_domain_id");
            $table->integer("cake_instance_id");
            $table->text("notes");
            $table->boolean("deployed");
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
