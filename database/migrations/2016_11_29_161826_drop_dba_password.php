<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropDbaPassword extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'doing_business_as' , function ( Blueprint $table ) {
            $table->dropColumn( 'password' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'doing_business_as' , function ( Blueprint $table ) {
            $table->string('password',100);
        } );
    }
}
