<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddfiredtoJobs extends Migration
{
    public function up()
    {
        Schema::table('job_entries', function(Blueprint $table) {
            $table->dateTime("time_fired")->before('time_started');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_entries', function (Blueprint $table) {
            $table->dropColumn("time_fired");
        });
    }
}
