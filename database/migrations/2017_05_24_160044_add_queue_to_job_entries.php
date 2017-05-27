<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQueueToJobEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_entries', function ($table) {

            $table->string('queue')->default('default');
            $table->index(['time_fired', 'queue']);

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

            $table->dropColumn('queue');
            $table->dropIndex('job_entries_time_fired_queue_index');

        });
    }
}
