<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAttributionAssignedRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->drop('attribution_assigned_records');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->create('attribution_assigned_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'client_id' );
            $table->integer( 'deploy_id' );
            $table->integer( 'delivered' )->default( 0 );
            $table->integer( 'opens' )->default( 0 );
            $table->integer( 'clicks' )->default( 0 );
            $table->integer( 'conversions' )->default( 0 );
            $table->integer( 'bounces' )->default( 0 );
            $table->integer( 'unsubs' )->default( 0 );
            $table->decimal( 'rev' , 11 , 3 )->default( 0.000 );
            $table->decimal( 'cost' , 9 , 3 )->default( 0.000 );
            $table->decimal( 'ecpm' , 7 , 3 )->default( 0.000 );
            $table->integer( 'date' );
            $table->timestamps();

            $table->index( 'client_id' );
            $table->index( 'deploy_id' );
            $table->index( 'date' );
        });
    }
}
