<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCFSToListCombine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("reporting_data")->table( 'creative_clickthrough_rates' , function ( Blueprint $table ) {
            $table->renameColumn( 'list_profile_id', "list_profile_combine_id" );
        } );

        Schema::connection("reporting_data")->table( 'from_open_rates' , function ( Blueprint $table ) {
            $table->renameColumn( 'list_profile_id', "list_profile_combine_id" );
        } );

        Schema::connection("reporting_data")->table( 'subject_open_rates' , function ( Blueprint $table ) {
            $table->renameColumn( 'list_profile_id', "list_profile_combine_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::connection("reporting_data")->table( 'creative_clickthrough_rates' , function ( Blueprint $table ) {
            $table->renameColumn("list_profile_combine_id", 'list_profile_id');
        } );

        Schema::connection("reporting_data")->table( 'from_open_rates' , function ( Blueprint $table ) {
            $table->renameColumn("list_profile_combine_id", 'list_profile_id');
        } );

        Schema::connection("reporting_data")->table( 'subject_open_rates' , function ( Blueprint $table ) {
            $table->renameColumn("list_profile_combine_id", 'list_profile_id' );
        } );
    }
}
