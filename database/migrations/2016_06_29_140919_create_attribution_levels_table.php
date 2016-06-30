<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributionLevelsTable extends Migration
{
    protected $connection = "attribution";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribution_levels', function (Blueprint $table) {
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
        Schema::drop('attribution_levels');
    }
}
