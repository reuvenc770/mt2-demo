<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExpandPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'permissions' , function ( Blueprint $table ) {
            $table->integer("rank");
            $table->integer("parent");
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'permissions' , function ( Blueprint $table ) {
            $table->dropColumn(["rank","parent"]);
        } );
    }
}
