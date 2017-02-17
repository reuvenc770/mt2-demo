<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixAWeberColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->table('a_weber_email_actions_storages', function (Blueprint $table) {
            $table->integer('esp_internal_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('reporting_data')->table('a_weber_email_actions_storages', function (Blueprint $table) {
            $table->mediumInteger('esp_internal_id')->change();
        });
    }
}
