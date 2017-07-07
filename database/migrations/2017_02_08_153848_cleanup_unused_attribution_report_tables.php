<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CleanupUnusedAttributionReportTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $clientReports = \DB::select( "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE 'attribution_client_report%' OR TABLE_NAME LIKE 'attribution_feed_report%' OR TABLE_NAME = 'attribution_record_reports'" );
        foreach ( $clientReports as $current ) {
            Schema::connection( 'attribution' )->dropIfExists( $current->TABLE_NAME );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //No turning back
    }
}
