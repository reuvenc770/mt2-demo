<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueCakeConversions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'reporting_data' )->table( 'cake_conversions' , function ( Blueprint $table ) {
            $table->index( 'conversion_date' );        
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
            $table->dropIndex( 'cake_conversions_conversion_date_index' );        
        } );
    }
}
