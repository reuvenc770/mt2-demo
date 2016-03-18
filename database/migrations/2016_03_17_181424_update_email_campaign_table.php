<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmailCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('email_campaign_statistics', function($table) {
            $table->string('last_status')->after('campaign_id')->default('esp load');
            $table->string('user_agent')->after('hard_bounce')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('email_campaign_statistics', function($table) {
            $table->dropColumn('last_status');
            $table->dropColumn('user_agent');
        });
    }
}
