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
            $table->string("deploy_name", 100);
            $table->date("send_date");
            $table->integer("esp_account_id");
            $table->string("external_deploy_id", 100);
            $table->integer("offer_id");
            $table->integer("creative_id");
            $table->integer("from_id");
            $table->integer("subject_id");
            $table->integer("template_id");
            $table->integer("mailing_domain_id");
            $table->integer("content_domain_id");
            $table->integer("cake_instance_id");
            $table->integer("cake_affiliate_id");
            $table->text("notes");
            $table->boolean("deployed");
        });

        Schema::create('deploy_list_profile', function (Blueprint $table) {
            $table->increments('deploy_id');
            $table->string("list_profile_id");

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
        Schema::drop('deploy_list_profile');
    }
}
