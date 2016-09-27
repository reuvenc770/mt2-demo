<?php
/**
 * @author Adam Chin <achin@zetainteractive.com >
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustCakeConversion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'reporting_data' )->table( 'cake_conversions' , function ( Blueprint $table ) {
            $table->dropColumn( 'click_date' );
            $table->dropColumn( 'campaign_date' );
            $table->dropColumn( 'user_agent_string' );

            $table->decimal( 'paid_usa' , 7 , 4 )->default( 0.0000 )->after( 'price_paid' );
            $table->decimal( 'received_usa' , 7 , 4 )->default( 0.0000 )->after( 'price_received' );
            $table->decimal( 'conversion_rate' , 7 , 4 )->default( 0.0000 )->after( 'price_received_currency_id' );
            $table->tinyInteger( 'is_click_conversion' )->default( 0 )->after( 'conversion_id' );

            $table->renameColumn( 'price_received' , 'received_raw' );
            $table->renameColumn( 'price_paid' , 'paid_raw' );
            $table->renameColumn( 'price_paid_currency_id' , 'paid_currency_id' );
            $table->renameColumn( 'price_received_currency_id' , 'received_currency_id' );

            $table->unique( [ 'click_id' , 'conversion_date' , 'is_click_conversion' ] , 'unique_conversion' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'reporting_data' )->table( 'cake_conversions' , function ( Blueprint $table ) {
            $table->dateTime( 'click_date' );
            $table->dateTime( 'campaign_date' );
            $table->text( 'user_agent_string' )->default( '' );

            $table->renameColumn( 'received_raw' , 'price_received' );
            $table->renameColumn( 'paid_raw' , 'price_paid' );
            $table->renameColumn( 'paid_currency_id' , 'price_paid_currency_id' );
            $table->renameColumn( 'received_currency_id' , 'price_received_currency_id' );

            $table->dropColumn( 'paid_usa' );
            $table->dropColumn( 'received_usa' );
            $table->dropColumn( 'conversion_rate' );
            $table->dropColumn( 'is_click_conversion' );

            $table->dropUnique( 'unique_conversion' );
        } );
    }
}
