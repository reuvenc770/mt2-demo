<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSourceUrlCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'reporting_data' )->create('source_url_counts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'feed_id' )->unsigned();
            $table->string( 'source_url' );
            $table->integer( 'count' )->unsigned()->default( 0 );
            $table->date( 'capture_date' );

            $table->index( 'feed_id' );
            $table->index( 'source_url' );
            $table->index( 'capture_date' );

            $table->unique( [ 'feed_id' , 'source_url' , 'capture_date'  ] , 'unique_count_index' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'reporting_data' )->drop('source_url_counts');
    }
}
