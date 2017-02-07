<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeClickIdColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'reporting_data' )->table( 'cake_conversions' , function ( $table ) {
            $table->string( 'click_id' )->change();
            $table->dropUnique( 'unique_conversion' );

            $table->unique( [ 'click_id' , 'conversion_id' ] , 'unique_conversion' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'reporting_data' )->table( 'cake_conversions' , function ( $table ) {
            $table->integer( 'click_id' )->change();
            $table->unique( [ 'click_id' , 'conversion_date' , 'is_click_conversion' ] , 'unique_conversion' );
        } );
    }
}
