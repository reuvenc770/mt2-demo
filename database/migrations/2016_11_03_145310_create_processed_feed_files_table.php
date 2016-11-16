<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessedFeedFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processed_feed_files', function (Blueprint $table) {
            $table->string( 'path' );
            $table->integer( 'feed_id' )->unsigned();
            $table->integer( 'line_count' );
            $table->timestamps();

            $table->primary( 'path' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('processed_feed_files');
    }
}
