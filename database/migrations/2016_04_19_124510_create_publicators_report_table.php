<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicatorsReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( "reporting_data" )->create( "publicators_reports" , function ( Blueprint $table ) {
            $table->increments( "id" );
            $table->integer( "esp_account_id" )->unsigned();
            $table->integer( "internal_id" )->unique();
            $table->dateTime( "sent_date" )->nullable();
            $table->integer( "total_sent" )->nullable();
            $table->integer( "total_opens" )->nullable();
            $table->integer( "total_clicks" )->nullable();
            $table->integer( "total_bounces" )->nullable();
            $table->integer( "total_unsubscribes" )->nullable();
        } );

        Schema::connection( "reporting_data" )->table( "publicators_reports" , function( $table ) {
            $dbName = env( "DB_DATABASE" );
            $table->foreign( "esp_account_id" )->references( "id" )->on( "{$dbName}.esp_accounts" );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( "reporting_data" )->drop( "publicators_reports" );
    }
}
