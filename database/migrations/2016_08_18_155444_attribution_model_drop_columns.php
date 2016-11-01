<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AttributionModelDropColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table('attribution_models', function (Blueprint $table) {
            $table->dropColumn( 'attribution_level_table' );
            $table->dropColumn( 'transient_records_table' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->table('attribution_models', function (Blueprint $table) {
            $table->string( 'attribution_level_table' )->after( 'live' );
            $table->string( 'transient_records_table' )->after( 'attribution_level_table' );
        });
    }
}
