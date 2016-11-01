<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AWeberReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->create('a_weber_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('esp_account_id')->unsigned();
            $table->string('subject', 100)->nullable();
            $table->string('internal_id')->unique();
            $table->string('info_url', 100)->nullable();
            $table->integer('total_sent')->nullable();
            $table->integer('total_opens')->nullable();
            $table->integer('unique_opens')->nullable();
            $table->integer('total_clicks')->nullable();
            $table->integer('unique_clicks')->nullable();
            $table->integer('total_unsubscribes')->nullable();
            $table->integer('total_undelivered')->nullable();
            $table->dateTime('sent_at')->nullable();
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
        Schema::connection('reporting_data')->drop('a_weber_reports');
    }
}
