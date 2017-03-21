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
        $schema = config('database.connections.list_profile.database');
        \DB::statement( "alter table {$schema}.list_profile_schedules modify last_run DATETIME" );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $schema = config('database.connections.list_profile.database');
        \DB::statement( "alter table {$schema}.list_profile_schedules modify last_run DATE" );
    }
}
