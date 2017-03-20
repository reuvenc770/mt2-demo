<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupNewAttributionReportingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->create( 'attribution_feed_reports' , function ( $table ) {
            $table->integer( 'feed_id' );
            $table->decimal( 'cpc_revenue' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->decimal( 'cpc_revshare' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->decimal( 'cpa_revenue' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->decimal( 'cpa_revshare' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->decimal( 'cpm_revenue' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->decimal( 'cpm_revshare' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->integer( 'uniques' )->unsigned()->default( 0.0000 );
            $table->date( 'date' );
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->index( 'feed_id' );
            $table->index( 'date' );
            $table->unique( [ 'feed_id' , 'date' ] , 'feed_date_unique' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->drop( 'attribution_feed_reports' ); 
    }
}
