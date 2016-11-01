<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToAttributionRecordTruths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table( 'attribution_record_truths' , function ( Blueprint $table ) {
            $table->index( 'additional_imports' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->table( 'attribution_record_truths' , function ( Blueprint $table ) {
            $table->dropIndex( 'attribution_record_truths_additional_imports_index' );
        } );
    }
}
