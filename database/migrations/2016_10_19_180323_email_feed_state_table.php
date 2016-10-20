<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmailFeedStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('email_feed_statuses', function(Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('feed_id')->unsigned()->default(0);
            $table->enum('status', ['Active', 'Deleted'])->default('Active');

            $table->primary(['email_id', 'feed_id']);
            $table->index(['feed_id', 'status'], 'feed_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('email_feed_status');
    }
}
