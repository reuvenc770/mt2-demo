<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRevshareClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'clients' , function ( Blueprint $table ) {
            $table->decimal( 'revshare' , 3 , 2 )->default( '0.15' )->after( 'status' );    
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'clients' , function ( Blueprint $table ) {
            $table->dropColumn( 'revshare' );    
        } );
    }
}
