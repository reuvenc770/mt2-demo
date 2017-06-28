<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->increments( 'id' );
            $table->string( 'content_key' );
            $table->text( 'content' );

            $table->timestamp( 'created_at' )->default( DB::raw('CURRENT_TIMESTAMP') );

            $table->index( 'content_key' );
            $table->index( 'created_at' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notification_logs');
    }
}
