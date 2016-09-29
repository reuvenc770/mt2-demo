<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcessingColumnAttributionModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table( 'attribution_models' , function ( $table ) {
            $table->tinyInteger( 'processing' )->default( 0 )->after( 'live' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->table( 'attribution_models' , function ( $table ) {
            $table->dropColumn( 'processing' );
        } );
    }
}
