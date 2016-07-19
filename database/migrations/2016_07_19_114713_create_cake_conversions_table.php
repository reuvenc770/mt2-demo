<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCakeConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'reporting_data' )->create('cake_conversions', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 's1' )->default( '' );
            $table->string( 's2' )->default( '' );
            $table->string( 's4' )->default( '' );
            $table->string( 's5' )->default( '' );
            $table->dateTime( 'clickDate' );
            $table->dateTime( 'campaignDate' );
            $table->integer( 'clickId' )->unsigned();
            $table->dateTime( 'conversionDate' )->nullable();
            $table->integer( 'conversionId' )->default( 0 );
            $table->integer( 'requestSessionId' );
            $table->integer( 'affiliateId' );
            $table->integer( 'offerId' );
            $table->integer( 'advertiserId' );
            $table->integer( 'campaignId' );
            $table->integer( 'creativeId' );
            $table->text( 'userAgentString' )->default( '' );
            $table->decimal( 'priceReceived' , 7 , 4 )->default( 0.0000 );
            $table->decimal( 'pricePaid' , 7 , 4 )->default( 0.0000 );
            $table->tinyInteger( 'pricePaidCurrencyId' )->default( 1 );
            $table->tinyInteger( 'priceReceivedCurrencyId' )->default( 1 );
            $table->string( 'ip' )->nullable();
            $table->timestamps();

            $table->index( 's1' );
            $table->index( 'campaignDate' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'reporting_data' )->drop('cake_conversions');
    }
}
