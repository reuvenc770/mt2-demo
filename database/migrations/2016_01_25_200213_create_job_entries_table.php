<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_name');
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamp('time_started');
            $table->timestamp('time_finished');
            $table->integer('attempts');
            $table->string('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('job_entries');
    }
}
