<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFeedAndClientNameToUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feeds' , function(Blueprint $table) {
            $table->unique( 'name' );
        });

        Schema::table('clients' , function(Blueprint $table) {
            $table->unique( 'name' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feeds', function(Blueprint $table){
            $table->dropUnique( 'name' );
        });

        Schema::table('clients', function(Blueprint $table){
            $table->dropUnique( 'name' );
        });
    }
}
