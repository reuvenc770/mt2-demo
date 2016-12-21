<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpContentServerStatsRaw extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('content_server_stats_raws', function (Blueprint $table) {
            $table->string( 'ip' )->nullable();
            $table->index( 'ip' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content_server_stats_raws', function (Blueprint $table) {
            $table->dropIndex( 'content_server_stats_raws_ip_index' );
            $table->dropColumn( 'ip' );
        } );
    }
}
