<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultUrlFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deploys' , function( Blueprint $table ) {
            $table->string( 'url_format' , 20)->default('short')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deploys' , function( Blueprint $table ) {
            $table->string( 'url_format' , 20)->default('new')->change();
        });
    }
}
