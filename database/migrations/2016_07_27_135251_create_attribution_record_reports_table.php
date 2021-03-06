<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributionRecordReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->create('attribution_record_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'email_id' )->unsigned();
            $table->integer( 'deploy_id' )->unsigned();
            $table->integer( 'offer_id' )->unsigned();
            $table->integer( 'delivered' )->unsigned()->default( 0 );
            $table->integer( 'opened' )->unsigned()->default( 0 );
            $table->integer( 'clicked' )->unsigned()->default( 0 );
            $table->integer( 'converted' )->unsigned()->default( 0 );
            $table->integer( 'bounced' )->unsigned()->default( 0 );
            $table->integer( 'unsubbed' )->unsigned()->default( 0 );
            $table->decimal( 'revenue' , 11 , 3 )->default( 0.0000 );
            $table->date( 'date' );
            $table->timestamps();

            $table->unique( [ 'email_id' , 'deploy_id' , 'offer_id' , 'date' ] , 'email_deploy_offer_date' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
