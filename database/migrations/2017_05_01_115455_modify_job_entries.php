<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyJobEntries extends Migration
{
    /**
     * modifications to job_entries for job monitoring.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_entries', function ($table) {

            $table->mediumInteger('runtime_seconds_threshold')->default(0);
            $table->string('acceptance_test')->nullable();
            $table->json('diagnostics')->nullable();

            $table->index(['time_started', 'status']);
            $table->index('status');

        });





    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_entries', function ($table) {

            $table->dropColumn('runtime_seconds_threshold');
            $table->dropColumn('acceptance_test');
            $table->dropColumn('diagnostics');

            $table->dropIndex('job_entries_status_index');
            $table->dropIndex('job_entries_time_started_status_index');

        });



    }
}
