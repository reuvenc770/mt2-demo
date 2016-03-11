<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueKeyToEmailCampaignStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('email_campaign_statistics', function($table) {
            $table->dropIndex('email_campaign_statistics_campaign_id_email_id_index');
            $table->unique(array('campaign_id', 'email_id'), 'campaign_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('email_campaign_statistics', function($table) {
            $table->dropUnique('campaign_email');
            $table->index(array('campaign_id', 'email_id'), 'email_campaign_statistics_campaign_id_email_id_index');
        });
    }
}
