<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueAttributionClientReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table( 'attribution_client_reports' , function ( Blueprint $table ) {
            $table->unique( [ 'client_stats_grouping_id' , 'date' ] , 'id_date_unique' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->table( 'attribution_client_reports' , function ( Blueprint $table ) {
            $table->dropUnique( 'id_date_unique' );
        } );
    }
}
