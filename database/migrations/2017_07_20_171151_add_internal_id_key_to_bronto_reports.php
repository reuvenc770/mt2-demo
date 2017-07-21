<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInternalIdKeyToBrontoReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('bronto_reports', function($table) {
            $table->index('internal_id', 'internal_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('bronto_reports', function($table) {
            $table->dropIndex('internal_id');
        });
    }
}
