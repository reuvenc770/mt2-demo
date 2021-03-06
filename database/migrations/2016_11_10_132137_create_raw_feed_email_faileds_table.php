<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawFeedEmailFailedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_feed_email_failed', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string( 'url' );
            $table->string( 'ip' );
            $table->string( 'email' );
            $table->bigInteger( 'feed_id' )->unsigned();
            if (App::environment('testing')) {
                $table->text('errors');
            } else {
                $table->json('errors');
            }
            $table->timestamps();

            $table->index( 'email' , 'email_index' );
            $table->index( 'feed_id' , 'feed_index' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('raw_feed_email_failed');
    }
}
