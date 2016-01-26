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
            $table->integer( 'campaign_id' )->nullable();
            $table->string( 'campaign_name' )->nullable();
            $table->string( 'status' )->nullable();
            $table->integer( 'is_active' )->nullable();
            $table->datetime( 'created' )->nullable();
            $table->datetime( 'scheduled_date' )->nullable();
            $table->string( 'fron_name' )->nullable();
            $table->string( 'from_email' )->nullable();
            $table->string( 'to_name' )->nullable();
            $table->integer( 'creative_id' )->nullable();
            $table->string( 'target' )->nullable();
            $table->string( 'subject' )->nullable();
            $table->string( 'archive_url' )->nullable();
            $table->integer( 'emails_sent' )->nullable();
            $table->integer( 'opens' )->nullable();
            $table->integer( 'unique_clicks' )->nullable();
            $table->integer( 'total_clicks' )->nullable();
            $table->integer( 'removes' )->nullable();
            $table->integer( 'forwards' )->nullable();
            $table->integer( 'forwards_from' )->nullable();
            $table->integer( 'hard_bounces' )->nullable();
            $table->integer( 'soft_bounces' )->nullable();
            $table->integer( 'complaints' )->nullable();
            $table->integer( 'delivered' )->nullable();
            $table->float( 'delivery_rate' )->nullable();
            $table->float( 'open_rate' )->nullable();
            $table->float( 'unique_rate' )->nullable();
            $table->float( 'ctr' )->nullable();
            $table->float( 'remove_rate' )->nullable();
            $table->float( 'bounce_rate' )->nullable();
            $table->float( 'soft_bounce_rate' )->nullable();
            $table->float( 'complaint_rate' )->nullable();
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
