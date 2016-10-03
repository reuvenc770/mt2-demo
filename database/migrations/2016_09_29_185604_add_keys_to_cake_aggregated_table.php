<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeysToCakeAggregatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('cake_aggregated_data', function($table) {
            $table->index(['email_id', 'subid_1', 'clickDate'], 'actions_join');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('cake_aggregated_data', function($table) {
            $table->dropIndex('actions_join');
        });
    }
}