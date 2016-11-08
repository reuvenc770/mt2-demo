<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDbaFieldToProxy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'proxies' , function ( Blueprint $table ) {
            $table->string( 'dba_name' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'proxies' , function ( Blueprint $table ) {
            $table->dropColumn( 'dba_name' );
        } );
    }
}
