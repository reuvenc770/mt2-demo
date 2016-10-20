<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProxies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'proxies' , function ( Blueprint $table ) {
            $table->renameColumn( 'esp_names' , 'esp_account_names' );
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
            $table->renameColumn('esp_account_names','esp_names');
        } );
    }
}
