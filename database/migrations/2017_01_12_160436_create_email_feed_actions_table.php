<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailFeedActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('email_feed_actions', function (Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('feed_id')->unsigned()->default(0);
            $table->enum('status', ['POR', 'POA', 'POL', 'Deliverable', 'Opener', 'Clicker', 'Converter'])->default('POR');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));


            $table->primary(['email_id', 'feed_id'], 'email_feed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('email_feed_actions');
    }
}
