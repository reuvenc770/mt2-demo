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
            $table->string("account_name",10);
            $table->string('name',50);
            $table->string('subject',150);
            $table->string('from_name',50);
            $table->string('from_email',50);
            $table->integer('sent');
            $table->integer('delivered');
            $table->integer('hard_bounces');
            $table->integer('soft_bounces');
            $table->integer('spam_bounces');
            $table->integer('opens');
            $table->integer('clicks');
            $table->integer('unsubs');
            $table->integer('spam_complaints');
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
        //
    }
}
