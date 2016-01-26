<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmailDirectReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'email_direct_reports' , function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->string( 'account_name' );
            $table->integer( 'internal_id' );
            $table->integer( 'campaign_id' );
            $table->string( 'campaign_name' );
            $table->string( 'status' );
            $table->integer( 'is_active' );
            $table->datetime( 'created' );
            $table->datetime( 'scheduled_date' );
            $table->string( 'fron_name' );
            $table->string( 'from_email' );
            $table->string( 'to_name' );
            $table->integer( 'creative_id' );
            $table->string( 'target' );
            $table->string( 'subject' );
            $table->string( 'archive_url' );
            $table->integer( 'emails_sent' );
            $table->integer( 'opens' );
            $table->integer( 'unique_clicks' );
            $table->integer( 'total_clicks' );
            $table->integer( 'removes' );
            $table->integer( 'forwards' );
            $table->integer( 'forwards_from' );
            $table->integer( 'hard_bounces' );
            $table->integer( 'soft_bounces' );
            $table->integer( 'complaints' );
            $table->integer( 'delivered' );
            $table->float( 'delivery_rate' );
            $table->float( 'open_rate' );
            $table->float( 'unique_rate' );
            $table->float( 'ctr' );
            $table->float( 'remove_rate' );
            $table->float( 'bounce_rate' );
            $table->float( 'soft_bounce_rate' );
            $table->float( 'complaint_rate' );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop( 'email_direct_reports' );
    }
}
