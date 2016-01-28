<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCampaignerDB extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigner_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("internal_id");
            $table->string("account_name",10)->nullable();
            $table->string('name',50)->nullable();
            $table->string('subject',150)->nullable();
            $table->string('from_name',50)->nullable();
            $table->string('from_email',50)->nullable();
            $table->integer('sent')->nullable();
            $table->integer('delivered')->nullable();
            $table->integer('hard_bounces')->nullable();
            $table->integer('soft_bounces')->nullable();
            $table->integer('spam_bounces')->nullable();
            $table->integer('opens')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('unsubs')->nullable();
            $table->integer('spam_complaints')->nullable();
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
        Schema::drop('campaigner_reports');
    }
}
