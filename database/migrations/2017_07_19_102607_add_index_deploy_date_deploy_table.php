<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexDeployDateDeployTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'deploys' , function ( Blueprint $table ) {
            $table->index( [ 'id' , 'send_date' ] , 'deploy_send_date' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'deploys' , function ( Blueprint $table ) {
            $table->dropIndex( 'deploy_send_date' );
        } );
    }
}
