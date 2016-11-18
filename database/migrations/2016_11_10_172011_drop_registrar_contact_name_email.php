<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropRegistrarContactNameEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'registrars' , function ( Blueprint $table ) {
            $table->dropColumn( [ 'contact_name' , 'contact_email' ] );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'registrars' , function ( Blueprint $table ) {
            $table->string( "contact_email", 50 )->after( 'username' );
            $table->string( "contact_name", 50 )->after( 'username' );
        } );
    }
}
