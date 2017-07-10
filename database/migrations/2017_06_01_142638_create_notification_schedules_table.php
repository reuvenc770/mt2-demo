<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_schedules', function (Blueprint $table) {
            $table->increments( 'id' );
            $table->string( 'content_key' );
            $table->string( 'level' );
            $table->tinyInteger( 'status' )->default( 1 );
            $table->string( 'title' );

            $table->string( 'cron_expression' )->default( '* 10 * * * *' );
            $table->smallInteger( 'content_lookback' )->default( 24 )->comment( 'in hours' );

            $table->tinyInteger( 'use_email' )->default( 0 );
            $table->text( 'email_recipients' )->nullable();
            $table->text( 'email_template_path' )->nullable();

            $table->tinyInteger( 'use_slack' )->default( 0 );
            $table->text( 'slack_recipients' )->nullable();
            $table->text( 'slack_template_path' )->nullable();

            $table->timestamp( 'created_at' )->default( DB::raw( 'CURRENT_TIMESTAMP' ) );
            $table->timestamp( 'updated_at' )->default( DB::raw( 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' ) );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notification_schedules');
    }
}
