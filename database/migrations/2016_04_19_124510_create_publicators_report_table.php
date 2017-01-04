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
            $table->dateTime( "CreatedDate" )->nullable();
            $table->dateTime( "SentDate" )->nullable();
            $table->string( "CampaignStatusDescription" , 100 )->nullable();
            $table->integer( "CampaignStatusType" )->nullable();
            $table->integer( "ListId" )->nullable();
            $table->string( "ListName" , 200 )->nullable();
            $table->integer( "CampaignEventType" )->nullable();
            $table->string( "CampaignEventTypeDescription" , 100 )->nullable();
            $table->string( "FromName" , 200 )->nullable();
            $table->string( "FromEmail" , 200 )->nullable();
            $table->string( "Subject" , 200 )->nullable();
            $table->string( "Address" , 100 )->nullable();
            $table->string( "Contact" , 100 )->nullable();
            $table->integer( "TotalMailsSent" )->nullable();
            $table->integer( "TotalOpened" )->nullable();
            $table->integer( "TotalUniqueOpened" )->nullable();
            $table->integer( "TotalOpenedFromSmartPhone" )->nullable();
            $table->integer( "TotalClicks" )->nullable();
            $table->integer( "TotalUniqueClicks" )->nullable();
            $table->integer( "TotalBounces" )->nullable();
            $table->integer( "TotalUniqueUnsubscribed" )->nullable();
            $table->integer( "TotalForwards" )->nullable();
            $table->integer( "TotalPurchases" )->nullable();
            $table->timestamps();
        } );

        Schema::connection( "reporting_data" )->table( "publicators_reports" , function( $table ) {
            $dbName = config("database.connections.mysql.database");
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
