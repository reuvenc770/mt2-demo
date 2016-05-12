<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRunAtToCampaigner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->table('campaigner_reports', function(Blueprint $table) {
            $table->dateTime("run_on")->after('from_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('reporting_data')->table('campaigner_reports', function(Blueprint $table) {
            $table->dropColumn("run_on");
        });
    }
}
