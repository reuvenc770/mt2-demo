<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustEspAccountStatToggle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'esp_accounts' , function ( Blueprint $table ) {
            $table->dropColumn( 'status' );
            $table->tinyInteger( 'enable_stats' )->default( 1 )->after( 'updated_at' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'esp_accounts' , function ( Blueprint $table ) {
            $table->tinyInteger( 'status' )->after( 'updated_at' );
            $table->dropColumn( 'enable_stats' );
        } );
    }
}
