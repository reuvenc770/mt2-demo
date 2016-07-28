<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributionDeployReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->create('attribution_deploy_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'deploy_id' )->unsigned();
            $table->integer( 'delivered' )->unsigned()->default( 0 );
            $table->integer( 'opened' )->unsigned()->default( 0 );
            $table->integer( 'clicked' )->unsigned()->default( 0 );
            $table->integer( 'converted' )->unsigned()->default( 0 );
            $table->integer( 'bounced' )->unsigned()->default( 0 );
            $table->integer( 'unsubbed' )->unsigned()->default( 0 );
            $table->decimal( 'revenue' , 11 , 3 )->unsigned()->default( 0.00 );
            $table->date( 'date' );
            $table->timestamps();

            $table->unique( [ 'deploy_id' , 'date' ] );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->drop('attribution_deploy_reports');
    }
}
