<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAttributionClientReportsToAttributionFeedReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('attribution')->rename( 'attribution_client_reports' , 'attribution_feed_reports' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('attribution')->rename( 'attribution_feed_reports' , 'attribution_client_reports' );
    }
}
