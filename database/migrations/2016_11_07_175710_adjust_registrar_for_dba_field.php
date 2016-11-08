<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustRegistrarForDbaField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'registrars' , function ( Blueprint $table ) {
            $table->dropColumn( ['phone_number' , 'address' , 'address_2' , 'city' , 'state' , 'zip' , 'entity_name'] );
            $table->text( 'dba_names' );
            $table->string( 'password' , 100 );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'registrars' , function (Blueprint $table) {
            $table->string( 'entity_name' , 100 )->after( 'cont act_credit_card' );
            $table->integer( 'zip' )->after( 'contact_credit_card' );
            $table->char( 'state' , 2 )->after( 'contact_credit_card' );
            $table->string( 'city' , 100 )->after( 'contact_credit_card' );
            $table->string( 'address_2' , 100)->after( 'contact_credit_card' );
            $table->string( 'address' , 100 )->after( 'contact_credit_card' );
            $table->string( 'phone_number' , 15 )->after( 'contact_email' );
            $table->dropColumn( 'dba_names' );
        } );
    }
}
