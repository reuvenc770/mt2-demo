<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdmiralOnlyListProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'list_profile' )->table( 'list_profiles' , function ( Blueprint $table ) {
            $table->tinyInteger( 'admiral_only' )->after( 'name' );
            $table->index( 'admiral_only' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'list_profile' )->table( 'list_profiles' , function ( Blueprint $table ) {
            $table->dropColumn( 'admiral_only' );
        } );
    }
}
