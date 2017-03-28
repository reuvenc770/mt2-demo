<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingAddingNeededKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::statement("ALTER TABLE job_entries ADD INDEX `job_name` (job_name(100))");

        Schema::table('deploys', function($table) {
            $table->index('party', 'party');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('job_entries', function($table) {
            $table->dropIndex('job_name');
        });

        Schema::table('deploys', function($table) {
            $table->index('party');
        });
    }
}
