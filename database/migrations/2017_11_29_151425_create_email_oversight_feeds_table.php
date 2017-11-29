<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailOversightFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_oversight_feeds', function (Blueprint $table) {
            $table->integer( 'feed_id' )->default( 0 );
            $table->integer( 'list_id' )->default( 0 );
            $table->timestamp( 'created_at' )->default( DB::raw( 'CURRENT_TIMESTAMP' ) );
            $table->timestamp( 'updated_at' )->default( DB::raw( 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' ) );

            $table->unique( 'feed_id' );
            $table->unique( 'list_id' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('email_oversight_feeds');
    }
}
