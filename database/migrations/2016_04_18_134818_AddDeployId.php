<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeployId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->table('a_weber_reports', function($table) {
            $table->string('campaign_name')->after('esp_account_id');
            $table->string('internal_id')->nullable()->change();
            $table->dropUnique('aweber_reports_internal_id_unique');
            $table->renameColumn('sent_at', 'datetime');
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
            $table->dropColumn("campaign_name");
            $table->string('internal_id')->unique();
        });
    }
}
