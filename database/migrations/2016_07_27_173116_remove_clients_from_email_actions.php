<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveClientsFromEmailActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('email_actions', function ($table) {
            $table->dropColumn('client_id');
        });

        Schema::connection('reporting_data')->table('email_campaign_statistics', function ($table) {
            $table->dropIndex('client_id');
            $table->dropColumn('client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // Probably shouldn't do this unless you absolutely have to
        
        Schema::connection('reporting_data')->table('email_actions', function($table) {
            $table->integer('client_id')->unsigned()->default(0);
        });

        Schema::connection('reporting_data')->table('email_campaign_statistics', function($table) {
            $table->integer('client_id')->unsigned()->default(0);
            $table->index('client_id', 'client_id');
        });
        
    }
}
