<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientAndUaToEcs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('email_campaign_statistics', function($table) {
            $table->integer('client_id')->unsigned()->default(0);
            $table->integer('user_agent_id')->unsigned()->default(0);
            $table->index('client_id', 'client_id');
            $table->index('user_agent_id', 'user_agent_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('email_campaign_statistics', function($table) {
            $table->dropIndex('client_id');
            $table->dropIndex('user_agent_id');
            $table->dropColumn('client_id');
            $table->dropColumn('user_agent_id');
        }); 
    }
}
