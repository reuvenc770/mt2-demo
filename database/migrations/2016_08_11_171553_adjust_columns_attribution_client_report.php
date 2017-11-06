<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustColumnsAttributionClientReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table( 'attribution_client_reports' , function ( Blueprint $table ) {
            $table->dropColumn( 'delivered' );
            $table->dropColumn( 'opened' );
            $table->dropColumn( 'clicked' );
            $table->dropColumn( 'converted' );
            $table->dropColumn( 'bounced' );
            $table->dropColumn( 'unsubbed' );
            $table->dropColumn( 'cost' );

            $table->integer( 'mt1_uniques' )->unsigned()->default( 0 )->after( 'revenue' );
            $table->integer( 'mt2_uniques' )->unsigned()->default( 0 )->after( 'revenue' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
