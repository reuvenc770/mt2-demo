<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributionLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->create('attribution_levels', function (Blueprint $table) {
            $table->integer( 'client_id' )->unsigned();
            $table->integer( 'level' )->unsigned();
            $table->timestamps();

            $table->primary( 'client_id' );
            $table->unique( 'level' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->drop('attribution_levels');
    }
}
