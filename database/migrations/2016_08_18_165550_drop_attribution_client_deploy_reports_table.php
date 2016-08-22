<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAttributionClientDeployReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->drop('attribution_client_deploy_reports');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->create('attribution_client_deploy_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'client_id' )->unsigned();
            $table->integer( 'deploy_id' )->unsigned();
            $table->integer( 'delivered' )->unsigned()->default( 0 );
            $table->integer( 'opened' )->unsigned()->default( 0 );
            $table->integer( 'clicked' )->unsigned()->default( 0 );
            $table->integer( 'converted' )->unsigned()->default( 0 );
            $table->integer( 'bounced' )->unsigned()->default( 0 );
            $table->integer( 'unsubbed' )->unsigned()->default( 0 );
            $table->decimal( 'revenue' , 11 , 3 )->unsigned()->default( 0.00 );
            $table->decimal( 'cost' , 9 , 3 )->unsigned()->default( 0.00 );
            $table->date( 'date' );
            $table->timestamps();

            $table->unique( [ 'deploy_id' , 'client_id' , 'date' ] , 'deploy_client_date' );
        });
    }
}
