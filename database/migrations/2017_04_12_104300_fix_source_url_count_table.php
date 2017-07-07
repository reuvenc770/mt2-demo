<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixSourceUrlCountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('source_url_counts', function($table) {
            $table->renameColumn('capture_date', 'subscribe_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('source_url_counts', function($table) {
            $table->renameColumn('subscribe_date', 'capture_date');
        });
    }
}
