<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeConversionIdCakeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'reporting_data' )->table( 'cake_conversions' , function ( Blueprint $table ) {
            $table->string('conversion_id')->change();
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
            $table->integer('conversion_id')->change();
        } );
    }
}
