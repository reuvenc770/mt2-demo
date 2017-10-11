<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LengthenIpFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('email_feed_instances', function($table) {
            $table->string('ip', 255)->change();
        });

        Schema::table('email_attributable_feed_latest_data', function($table) {
            $table->string('ip', 255)->change();
        });

        Schema::table('first_party_record_data', function($table) {
            $table->string('ip', 255)->change();
        });

        Schema::connection('eporting_data')->table('cake_actions', function($table) {
            $table->string('ip_address', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('email_feed_instances', function($table) {
            $table->string('ip', 15)->change();
        });

        Schema::table('email_attributable_feed_latest_data', function($table) {
            $table->string('ip', 16)->change();
        });

        Schema::table('first_party_record_data', function($table) {
            $table->string('ip', 16)->change();
        });

        Schema::connection('eporting_data')->table('cake_actions', function($table) {
            $table->string('ip_address', 30)->change();
    }
}
