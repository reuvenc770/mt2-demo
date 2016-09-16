<?php
/**
 * @author Adam Chin
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveActionsRecordReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table( 'attribution_record_reports' , function (Blueprint $table) {
            $table->dropColumn( 'delivered' );
            $table->dropColumn( 'opened' );
            $table->dropColumn( 'clicked' );
            $table->dropColumn( 'converted' );
            $table->dropColumn( 'bounced' );
            $table->dropColumn( 'unsubbed' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->table( 'attribution_record_reports' , function (Blueprint $table) {
            $table->integer( 'delivered' )->unsigned()->default( 0 );
            $table->integer( 'opened' )->unsigned()->default( 0 );
            $table->integer( 'clicked' )->unsigned()->default( 0 );
            $table->integer( 'converted' )->unsigned()->default( 0 );
            $table->integer( 'bounced' )->unsigned()->default( 0 );
            $table->integer( 'unsubbed' )->unsigned()->default( 0 );
        } );
    }
}
