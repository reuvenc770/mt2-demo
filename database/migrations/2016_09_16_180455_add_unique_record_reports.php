<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueRecordReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table( 'attribution_record_reports' , function (Blueprint $table) {
            $table->unique( [ 'email_id' , 'deploy_id' , 'offer_id' , 'date' ] , 'unique_record' );
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
            $table->dropUnique( 'unique_record' );
        } );
    }
}
