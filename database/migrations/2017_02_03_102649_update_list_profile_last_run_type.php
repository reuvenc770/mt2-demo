<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateListProfileLastRunType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement( 'alter table list_profile.list_profile_schedules modify last_run DATETIME' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement( 'alter table list_profile.list_profile_schedules modify last_run DATE' );
    }
}
