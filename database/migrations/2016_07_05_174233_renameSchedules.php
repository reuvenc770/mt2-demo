<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table('attribution_activity_schedules', function (Blueprint $table) {
            $table->renameColumn('inactive_date', 'trigger_date');
        });

        Schema::connection( 'attribution' )->table('attribution_expiration_schedules', function (Blueprint $table) {
            $table->renameColumn('expiration_date', 'trigger_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->table('attribution_activity_schedules', function (Blueprint $table) {
            $table->renameColumn('trigger_date', 'inactive_date');
        });

        Schema::connection( 'attribution' )->table('attribution_expiration_schedules', function (Blueprint $table) {
            $table->renameColumn('trigger_date', 'expiration_date');
        });
    }
}
