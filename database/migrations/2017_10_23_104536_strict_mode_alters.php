<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StrictModeAlters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('suppressions', function($table) {
            $table->integer('esp_internal_id')->unsigned()->default(0)->change();
        });

        Schema::table('invalid_email_instances', function($table) {
            $table->date('dob')->nullable()->change();
        });

        DB::statement("ALTER TABLE list_profile.list_profile_schedules MODIFY `day_of_week` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','') COLLATE utf8mb4_unicode_ci NOT NULL default ''");

        Schema::table('deploys', function ($table) {
            $table->integer('list_profile_combine_id')->default(0)->change();
        });

        Schema::connection('reporting_data')->table('campaigner_reports', function($table) {
            $table->string('from_name', 255)->nullable()->default(null)->change();
        });

        DB::statement("ALTER TABLE email_feed_instances MODIFY gender enum('', 'M', 'F', 'UNK') NOT NULL DEFAULT 'UNK'");

        Schema::connection('reporting_data')->table('maro_reports', function($table) {
            $table->dateTime('send_at')->nullable()->default(null)->change();
            $table->dateTime('sent_at')->nullable()->default(null)->change();
            $table->dateTime('maro_created_at')->nullable()->default(null)->change();
            $table->dateTime('maro_updated_at')->nullable()->default(null)->change();
        });

        Schema::connection('reporting_data')->table('blue_hornet_reports', function($table) {
            $table->string('bill_codes', 255)->nullable()->default('')->change();
        });

        Schema::connection('reporting_data')->table('cake_actions', function($table) {
            $table->bigInt('email_id')->unsigned()->default(0)->change(); // not getting reverted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('suppressions', function($table) {
            $table->integer('esp_internal_id')->change();
        });

        Schema::table('invalid_email_instances', function($table) {
            $table->date('dob')->change();
        });

        DB::statement("ALTER TABLE list_profile.list_profile_schedules MODIFY `day_of_week` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','') COLLATE utf8mb4_unicode_ci NOT NULL");
    
        Schema::table('deploys', function ($table) {
            $table->integer('list_profile_combine_id')->change();
        });

        Schema::connection('reporting_data')->table('campaigner_reports', function($table) {
            $table->string('from_name', 50)->nullable()->default(null)->change();
        });

        DB::statement("ALTER TABLE email_feed_instances MODIFY gender enum('', 'M', 'F') NOT NULL DEFAULT ''");

        Schema::connection('reporting_data')->table('maro_reports', function($table) {
            $table->dateTime('send_at')->nullable()->change();
            $table->dateTime('sent_at')->nullable()->change();
            $table->dateTime('maro_created_at')->nullable()->change();
            $table->dateTime('maro_updated_at')->nullable()->change();
        });

        Schema::connection('reporting_data')->table('blue_hornet_reports', function($table) {
            $table->integer('bill_codes')->nullable()->change();
        });
    }
}
