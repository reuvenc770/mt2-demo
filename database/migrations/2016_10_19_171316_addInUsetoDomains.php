<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInUsetoDomains extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'domains' , function ( Blueprint $table ) {
           $table->boolean("in_use");
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
            $table->dropColumn('in_use');
        } );
    }
}
