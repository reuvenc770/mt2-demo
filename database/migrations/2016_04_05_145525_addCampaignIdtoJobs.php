<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCampaignIdtoJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'job_entries' , function ( $table ) {
            $table->integer( 'campaign_id' )->default( 0 )->after( 'account_number' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'job_entries' , function ( $table ) {
            $table->dropColumn( 'campaign_id' );
        } );
    }
}
