<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsRawFeedEmailFailed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'raw_feed_email_failed' , function ( Blueprint $table ) {
            $table->tinyInteger( 'realtime' )->after( 'id' );
            $table->text( 'csv' )->nullable()->after( 'url' );
            $table->string( 'file' )->nullable()->after( 'csv' );
            $table->mediumInteger( 'line_number' )->nullable()->after( 'file' );
        
            $table->index( 'realtime' , 'realtime_index' );
            $table->index( 'file' , 'file_index' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'raw_feed_email_failed' , function ( Blueprint $table ) {
            $table->dropColumn( 'realtime' );
            $table->dropColumn( 'csv' );
            $table->dropColumn( 'file' );
            $table->dropColumn( 'line_number' );
        } );
    }
}
