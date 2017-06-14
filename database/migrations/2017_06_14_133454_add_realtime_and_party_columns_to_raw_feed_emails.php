<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRealtimeAndPartyColumnsToRawFeedEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'raw_feed_emails' , function ( Blueprint $table ) {
            $table->tinyInteger( 'realtime' )->nullable()->default( 1 )->after( 'feed_id' );
            $table->tinyInteger( 'party' )->nullable()->default( 3 )->after( 'feed_id' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'raw_feed_emails' , function ( Blueprint $table ) {
            $table->dropColumn( 'realtime' );
            $table->dropColumn( 'party' );
        } );
    }
}
