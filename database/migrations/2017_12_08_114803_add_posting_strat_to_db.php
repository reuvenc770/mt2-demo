<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostingStratToDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('esp_data_exports', function($table) {
            $table->string('posting_class_name')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('esp_data_exports', function($table) {
            $table->dropColumn('posting_string_name');
        });
    }
}
