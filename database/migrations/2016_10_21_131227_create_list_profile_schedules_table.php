<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListProfileSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_profile_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('list_profile_id')->unsigned()->default(0);
            $table->integer('offer_id')->unsigned()->default(0);
            $table->boolean('run_daily')->default(0);
            $table->boolean('run_weekly')->default(0);
            $table->enum('day_of_week', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', null]);
            $table->boolean('run_monthly')->default(0);
            $table->tinyInteger('day_of_month')->nullable()->default(null);
            $table->date('last_run')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('list_profile_schedules');
    }
}
