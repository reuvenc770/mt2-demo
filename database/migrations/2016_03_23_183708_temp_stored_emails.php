<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TempStoredEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('temp_stored_emails', function(Blueprint $table) {
            $table->bigInteger('email_id')->default(0);
            $table->integer('client_id')->default(0);
            $table->string('email_addr', 100)->default('');
            $table->string('status', 15)->default('Active');
            $table->string('first_name', 20)->default('');
            $table->string('last_name', 40)->default('');
            $table->string('address', 50)->default('');
            $table->string('address2', 50)->default('');
            $table->string('city', 50)->default('');
            $table->string('state', 2)->default('');
            $table->string('zip', 10)->default('');
            $table->string('country', 3)->default('');
            $table->date('dob')->default('0000-00-00');
            $table->string('gender', 1)->default('');
            $table->string('phone', 15)->default('');
            $table->string('mobile_phone', 15)->default('');
            $table->string('work_phone', 15)->default('');
            $table->date('capture_date')->default('0000-00-00');
            $table->string('ip');
            $table->string('source_url', 50)->default('');
            $table->datetime('unsubscribe_datetime');
            $table->timestamp('last_updated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('temp_stored_emails');
    }
}
