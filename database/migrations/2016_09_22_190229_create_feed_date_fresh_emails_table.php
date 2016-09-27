<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedDateFreshEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_date_email_breakdowns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('feed_id')->default(0);
            $table->date('date')->nullable();
            $table->integer('total_emails');
            $table->integer('valid_emails');
            $table->integer('suppressed_emails');
            $table->integer('fresh_emails');
            $table->integer('feed_duplicates'); // same email sent over the same feed
            $table->integer('cross_feed_duplicates'); // non-fresh email sent by another feed

            $table->unique(['feed_id', 'date'], 'feed_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('feed_month_email_breakdowns');
    }
}
