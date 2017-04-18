<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpmDeployOverrideTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'cpm_deploy_overrides' , function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'deploy_id' );
            $table->decimal( 'amount' , 11 , 3 );
            $table->date( 'start_date' );
            $table->date( 'end_date' );
            $table->timestamps();

            $table->index( 'deploy_id' , 'deploy_index' );
            $table->index( 'start_date' , 'start_date_index' );
            $table->index( 'end_date' , 'end_date_index' );
            $table->unique( [ 'deploy_id' , 'start_date' , 'end_date' ] , 'date_deploy_unique' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop( 'cpm_deploy_overrides' );
    }
}
