<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPasswordToFeeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'feeds' , function ( Blueprint $table ) {
            $table->string( 'password' )->after( 'short_name' );

            $table->index( 'password' , 'password_index' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'feeds' , function ( Blueprint $table ) {
            $table->dropColumn( 'password' );
        } );
    }
}
