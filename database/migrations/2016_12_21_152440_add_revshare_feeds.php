<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRevshareFeeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'feeds' , function ( Blueprint $table ) {
            $table->decimal( 'revshare' , 3 , 2 )->nullable()->after( 'status' );    
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
            $table->dropColumn( 'revshare' );    
        } );
    }
}
