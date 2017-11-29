<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFrontendFeaturePermissionMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('frontend_feature_permission_mappings', function (Blueprint $table) {
            $table->integer('frontend_feature_id')->unsigned();
            $table->integer('permission_id')->unsigned();

            $table->index( 'frontend_feature_id' , 'frontend_feature_index' );
            $table->unique( [ 'frontend_feature_id' , 'permission_id' ] , 'frontend_feature_perm_unique' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('frontend_feature_permission_mappings');
    }
}
