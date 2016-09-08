<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUniqueAttributionLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table('attribution_levels', function (Blueprint $table) {
            $table->dropUnique( 'attribution_levels_level_unique' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->table('attribution_levels', function (Blueprint $table) {
            $table->unique( 'level' );
        } );
    }
}
