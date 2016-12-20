<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentServerStatsRawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_server_stats_raws', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger( 'eid' )->unsigned();
            $table->bigInteger( 'link_id' )->unsigned();
            $table->integer( 'sub_aff_id' )->unsigned();
            $table->tinyInteger( 'action_id' )->unsigned()->default( 0 );
            $table->text( 'user_agent' );
            $table->text( 'referrer' );
            $table->text( 'query_string' );
            $table->dateTime( 'action_datetime' );

            $table->index( 'eid' );
            $table->index( 'link_id' );
            $table->index( 'sub_aff_id' );
            $table->index( 'action_id' );
            $table->index( 'action_datetime' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('content_server_stats_raws');
    }
}
