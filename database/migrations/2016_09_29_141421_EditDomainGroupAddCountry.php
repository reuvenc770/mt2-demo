<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditDomainGroupAddCountry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'domain_groups' , function ( Blueprint $table ) {
            $table->enum( 'country', ["US","UK"] )->default("US");
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'domain_groups' , function ( Blueprint $table ) {
            $table->dropColumn('country');
        } );
    }
}
