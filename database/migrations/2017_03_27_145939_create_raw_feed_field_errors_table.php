<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawFeedFieldErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_feed_field_errors', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'field' );
            $table->text( 'value' );
            $table->text( 'errors' );
            $table->bigInteger( 'raw_feed_email_failed_id' );
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->index( 'field' , 'field_index' );
            $table->index( 'raw_feed_email_failed_id' , 'raw_feed_email_failed_index' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('raw_feed_field_errors');
    }
}
