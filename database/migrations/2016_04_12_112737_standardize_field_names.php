<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StandardizeFieldNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::connection('reporting_data')->table('standard_reports', function($table) {
            DB::connection('reporting_data')->statement('ALTER TABLE standard_reports MODIFY COLUMN sub_id INTEGER AFTER id');
            $table->renameColumn('sub_id', 'external_deploy_id');
            $table->renameColumn('deploy_id', 'campaign_name');
            $table->integer('esp_internal_id')->after('esp_account_id')->default(0);
            $table->index('external_deploy_id' , 'external_deploy_id');
        });

        Schema::connection('reporting_data')->table('email_actions', function($table) {
            $table->renameColumn('campaign_id', 'esp_internal_id');
            $table->bigInteger('deploy_id')->after('client_id');
            $table->dropUnique('email_actions_email_id_campaign_id_datetime_unique');
            $table->unique(['email_id', 'deploy_id', 'datetime'], 'email_deploy_time');
            $table->index(['deploy_id', 'datetime'], 'deploy_date');
            $table->index(['esp_internal_id', 'datetime'], 'esp_internal_id_date');
        });

        Schema::table('orphan_emails', function($table) {
            $table->bigInteger('deploy_id')->unsigned()->after('esp_account_id');
            $table->renameColumn('campaign_id', 'esp_internal_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::connection('reporting_data')->table('standard_reports', function($table) {
            $table->renameColumn('external_deploy_id', 'sub_id');
            $table->renameColumn('campaign_name', 'deploy_id');
            $table->dropColumn('esp_internal_id');
            $table->dropIndex( 'external_deploy_id' );
        });

        Schema::connection('reporting_data')->table('email_actions', function($table) {
            $table->renameColumn('esp_internal_id', 'campaign_id');
            $table->dropColumn('deploy_id');
            $table->unique(['email_id', 'campaign_id', 'datetime'], 'email_actions_email_id_campaign_id_datetime_unique');
            $table->dropUnique('email_deploy_time');
        });

        Schema::table('orphan_emails', function($table) {
            $table->dropColumn('deploy_id');
            $table->renameColumn('esp_internal_id', 'campaign_id');
        });
    }
}
