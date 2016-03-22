<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SwitchRowsInEmailActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('email_actions', function(Blueprint $table) {
            $table->renameColumn('esp_id', 'esp_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('email_actions', function(Blueprint $table) {
            $table->renameColumn('esp_account_id', 'esp_id');
        });
    }
}
