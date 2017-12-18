<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLimitsAndLockingToExportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('esp_data_exports', function($table) {
            $table->tinyInteger('is_locked')->unsigned()->default(0)->after('status');
            $table->integer('fifteen_minute_limit')->unsigned()->nullable()->after('is_locked');
            $table->integer('day_limit')->unsigned()->nullable()->after('fifteen_minute_limit');
        });

        Schema::table('esp_workflow_log', function($table) {
            $table->tinyInteger('was_exported')->unsigned()->default(0)->after('email_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('esp_data_exports', function($table) {
            $table->dropColumn('is_locked');
            $table->dropColumn('fifteen_minute_limit');
            $table->dropColumn('day_limit');
        });

        Schema::table('esp_workflow_log', function($table) {
            $table->dropColumn('was_exported');
        });
    }
}
