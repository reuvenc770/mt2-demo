<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeployIdToAWeber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->table('a_weber_reports', function($table) {
            $table->string('deploy_id')->after('esp_account_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('reporting_data')->table('a_weber_reports', function(Blueprint $table) {
           $table->dropColumn(['deploy_id']);
        });
    }
}
