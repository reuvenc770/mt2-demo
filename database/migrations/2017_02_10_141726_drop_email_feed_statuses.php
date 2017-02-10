<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropEmailFeedStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::drop('email_feed_statuses');
        Schema::drop('email_feed_actions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::create('email_feed_statuses', function(Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('feed_id')->unsigned()->default(0);
            $table->enum('status', ['Active', 'Deleted'])->default('Active');

            $table->primary(['email_id', 'feed_id']);
            $table->index(['feed_id', 'status'], 'feed_status');
        });

        Schema::create('email_feed_actions', function (Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('feed_id')->unsigned()->default(0);
            $table->enum('status', ['POR', 'POA', 'MOA', 'Deliverable', 'Opener', 'Clicker', 'Converter'])->default('POR');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->primary(['email_id', 'feed_id'], 'email_feed');
        });
    }
}