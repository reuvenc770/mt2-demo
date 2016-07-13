<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GetRepsonceRawReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->create('get_response_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('esp_account_id')->unsigned();
            $table->string('name', 100)->nullable();
            $table->string('subject', 100)->nullable();
            $table->string('internal_id')->unique();
            $table->string('info_url', 100)->nullable();
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->string('reply_name')->nullable();
            $table->string('reply_email')->nullable();
            $table->text("html")->nullable();
            $table->integer('sent')->nullable();
            $table->integer('total_open')->nullable();
            $table->integer('unique_open')->nullable();
            $table->integer('total_click')->nullable();
            $table->integer('unique_click')->nullable();
            $table->integer('unsubscribes')->nullable();
            $table->integer('bounces')->nullable();
            $table->integer('complaints')->nullable();
            $table->dateTime('sent_on')->nullable();
            $table->dateTime('created_on')->nullable();
            $table->timestamps();

        });

        Schema::connection("reporting_data")->table('get_response_reports', function($table) {
            $dbName = env('DB_DATABASE','homestead');
            $table->foreign('esp_account_id')->references('id')->on("{$dbName}.esp_accounts");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('reporting_data')->drop('get_response_reports');
    }
}
