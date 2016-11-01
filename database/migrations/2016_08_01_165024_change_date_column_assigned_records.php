<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateColumnAssignedRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table('attribution_assigned_records', function (Blueprint $table) {
            $table->dropIndex( 'attribution_assigned_records_days_back_index' );
            $table->dropColumn( 'days_back' );

            $table->date( 'date' )->after( 'ecpm' );
            $table->index( 'date' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->table('attribution_assigned_records', function (Blueprint $table) {
            $table->dropIndex( 'attribution_assigned_records_date_index' );
            $table->dropColumn( 'date' );

            $table->integer( 'days_back' )->after( 'ecpm' );
            $table->index( 'days_back' );
        });
    }
}
