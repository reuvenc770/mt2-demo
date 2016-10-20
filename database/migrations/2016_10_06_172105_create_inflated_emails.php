<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInflatedEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('inflated_email_histories', function(Blueprint $table) {
            $table->bigInteger('final_email_id')->default(0);
            $table->bigInteger('old_email_id')->default(0);

            $table->primary(['final_email_id', 'old_email_id']);
            $table->index(['old_email_id', 'final_email_id'], 'old_final');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('inflated_email_histories');
    }
}