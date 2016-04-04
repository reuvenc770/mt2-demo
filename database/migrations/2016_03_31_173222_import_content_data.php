<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportContentData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        // table for holding content server actions

        Schema::create('content_server_actions', function (Blueprint $table) {
            $table->bigInteger('email_id')->default(0);
            $table->integer('sub_id')->default(0);
            $table->string('action_type', 30)->default('');
            $table->date('send_date')->nullable();
            $table->datetime('action_time')->nullable();

            $table->unique(['email_id', 'sub_id', 'action_type', 'action_time'], 'user_action');
            $table->index('send_date', 'send_date');
            $table->index(['email_id', 'send_date'], 'email_date');
            $table->index(['email_id', 'sub_id', 'send_date'], 'email_campaign_send');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('content_server_actions');
    }
}