<?php
/**
 * @author Adam Chin <achin@zetalglobal.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpmDeploySnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'reporting_data' )->create( 'deploy_snapshots' , function ( Blueprint $table ) {
            $table->string( 'email_address' );
            $table->integer( 'deploy_id' )->unsigned()->default( 0 );
            $table->integer( 'feed_id' )->unsigned()->default( 0 );
            $table->timestamp( 'created_at' )->default( DB::raw( 'CURRENT_TIMESTAMP' ) );
            $table->timestamp( 'updated_at' )->default( DB::raw( 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' ) );

            $table->index( 'deploy_id' , 'deploy_id_index' );
            $table->unique( [ 'email_address' , 'deploy_id' , 'feed_id' ] , 'email_deploy_feed_unique' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'reporting_data' )->drop( 'deploy_snapshots' );
    }
}
