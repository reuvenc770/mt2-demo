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
            $table->string( 'email_id' )->default( 0 );
            $table->string( 's1' )->default( '' );
            $table->string( 's2' )->default( '' );
            $table->string( 's3' )->default( '' );
            $table->string( 's4' )->default( '' );
            $table->string( 's5' )->default( '' );
            $table->dateTime( 'click_date' );
            $table->dateTime( 'campaign_date' );
            $table->integer( 'click_id' )->unsigned();
            $table->dateTime( 'conversion_date' )->nullable();
            $table->integer( 'conversion_id' )->default( 0 );
            $table->integer( 'request_session_id' );
            $table->integer( 'affiliate_id' );
            $table->integer( 'offer_id' );
            $table->integer( 'advertiser_id' );
            $table->integer( 'campaign_id' );
            $table->integer( 'creative_id' );
            $table->text( 'user_agent_string' )->default( '' );
            $table->decimal( 'price_received' , 7 , 4 )->default( 0.0000 );
            $table->decimal( 'price_paid' , 7 , 4 )->default( 0.0000 );
            $table->tinyInteger( 'price_paid_currency_id' )->default( 1 );
            $table->tinyInteger( 'price_received_currency_id' )->default( 1 );
            $table->string( 'ip' )->nullable();
            $table->timestamps();

            $table->index( 's1' );
            $table->index( 'campaign_date' );
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
