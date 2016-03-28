<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeDatesAndTimesNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // reporting data
        Schema::connection('reporting_data')->table('maro_reports', function($table) {
            $table->dateTime('send_at')->nullable()->change();
            $table->dateTime('sent_at')->nullable()->change();
            $table->dateTime('maro_created_at')->nullable()->change();
            $table->dateTime('maro_updated_at')->nullable()->change();
        });

        Schema::connection('reporting_data')->table('cake_aggregated_data', function($table) {
            $table->date('clickDate')->nullable()->change();
        });

        Schema::connection('reporting_data')->table('email_campaign_statistics', function($table) {
            $table->datetime('esp_first_open_datetime')->nullable()->change();
            $table->datetime('esp_last_open_datetime')->nullable()->change();
            $table->datetime('esp_first_click_datetime')->nullable()->change();
            $table->datetime('esp_last_click_datetime')->nullable()->change();
            $table->datetime('trk_first_open_datetime')->nullable()->change();
            $table->datetime('trk_last_open_datetime')->nullable()->change();
            $table->datetime('trk_first_click_datetime')->nullable()->change();
            $table->datetime('trk_last_click_datetime')->nullable()->change();
            $table->datetime('mt_first_open_datetime')->nullable()->change();
            $table->datetime('mt_last_open_datetime')->nullable()->change();
            $table->datetime('mt_first_click_datetime')->nullable()->change();
            $table->datetime('mt_last_click_datetime')->nullable()->change();
        });

        // non-reporting db
        Schema::table('email_client_instances', function($table) {
            $table->date('subscribe_date')->nullable()->change();
            $table->time('subscribe_time')->nullable()->change();
            $table->date('unsubscribe_date')->nullable()->change();
            $table->time('unsubscribe_time')->nullable()->change();
            $table->date('capture_date')->nullable()->change();
        });

        Schema::table('temp_stored_emails', function($table) {
            $table->datetime('unsubscribe_datetime')->nullable()->change();
            $table->date('capture_date')->nullable()->change();
            $table->date('dob')->nullable()->change();
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
