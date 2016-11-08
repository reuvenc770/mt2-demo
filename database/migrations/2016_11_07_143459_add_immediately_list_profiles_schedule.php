<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImmediatelyListProfilesSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'list_profile' )->table( 'list_profile_schedules' , function ( Blueprint $table ) {
            $table->tinyInteger( 'run_immediately' )->default( 0 )->after( 'list_profile_id' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'list_profile' )->table( 'list_profile_schedules' , function ( Blueprint $table ) {
            $table->dropColumn( 'run_immediately' );
        } );
    }
}
