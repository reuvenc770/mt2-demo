<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEspInternalIdDeployIdMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'reporting_data' )->create('esp_internal_id_deploy_id_maps', function (Blueprint $table) {
            $table->integer( 'esp_internal_id' )->unsigned();
            $table->integer( 'deploy_id' )->unsigned();
            $table->timestamps();

            $table->unique( [ 'esp_internal_id' , 'deploy_id' ] );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('esp_internal_id_deploy_id_maps');
    }
}
