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
            $table->unique( 'name' , 'unique_name' );
        });

        Schema::table('clients' , function(Blueprint $table) {
            $table->unique( 'name' , 'unique_name' );
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
            $table->dropUnique( 'unique_name' );
        });

        Schema::table('clients', function(Blueprint $table){
            $table->dropUnique( 'unique_name' );
        });
    }
}
