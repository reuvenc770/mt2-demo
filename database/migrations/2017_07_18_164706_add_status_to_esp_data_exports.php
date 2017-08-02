<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToEspDataExports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('esp_data_exports', function($table) {
            $table->boolean('status')->after('target_list');
            $table->index('status', 'status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('esp_data_exports', function($table) {
            $table->dropColumn('status');
        });
    }
}
