<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OpenVsLinkTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('esp_field_options', function($table) {
            $table->string('open_email_id_field', 255)->default('')->after('email_id_field');
            $table->string('open_email_address_field', 255)->default('')->after('email_address_field');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('esp_field_options', function($table) {
            $table->dropColumn('open_email_id_field');
            $table->dropColumn('open_email_address_field');
        });
    }
}
