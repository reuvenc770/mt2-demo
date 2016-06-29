<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributionModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribution_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'name' );
            $table->boolean( 'live' );
            $table->string( 'temp_level_table' );
            $table->string( 'temp_transient_table' );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attribution_models');
    }
}
