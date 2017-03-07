<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::drop('email_feed_instances');

        Schema::create('email_feed_instances', function(Blueprint $table) {
            $table->bigIncrements();
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('feed_id')->unsigned()->default(0);
            $table->date('subscribe_date');
            $table->datetime('subscribe_datetime');
            $table->date('capture_date');
            $table->string('first_name', 20)->default('');
            $table->string('last_name', 40)->default('');
            $table->string('address', 50)->default('');
            $table->string('address2', 50)->default('');
            $table->string('city', 50)->default('');
            $table->char('state', 2);
            $table->string('zip', 10)->default('');
            $table->char('country')->default('');
            $table->date('dob')->nullable();
            $table->enum('gender', array('M', 'F', 'UNK'))->default('UNK');
            $table->string('phone', 15)->default('');
            $table->string('mobile_phone', 15)->default('');
            $table->string('work_phone', 15)->default('');
            $table->string('source_url', 50)->default('');
            $table->string('ip', 15)->default('10.1.2.3');
            $table->json('other_fields');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->unique(['email_id', 'feed_id', 'subscribe_date'], 'email_feed_date');
            $table->index('feed_id', 'feed_id');
            $table->index('subscribe_date', 'subscribe_date');
            $table->index('capture_date', 'capture_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('email_feed_instances');

        // This is all over the place
        Schema::create('email_feed_instances', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('feed_id')->unsigned()->default(0);
            $table->date('subscribe_datetime')->nullable();
            $table->date('unsubscribe_datetime')->nullable();
            $table->enum('status', array('A', 'B', 'C', 'U'));
            $table->string('first_name', 20)->default('');
            $table->string('last_name', 40)->default('');
            $table->string('address', 50)->default('');
            $table->string('address2', 50)->default('');
            $table->string('city', 50)->default('');
            $table->char('state', 2);
            $table->string('zip', 10)->default('');
            $table->char('country')->default('');
            $table->date('dob')->nullable();
            $table->enum('gender', array('', 'M', 'F'))->default('');
            $table->string('phone', 15)->default('');
            $table->string('mobile_phone', 15)->default('');
            $table->string('work_phone', 15)->default('');
            $table->date('capture_date'); // this really should not be null
            $table->string('source_url', 50)->default('');
            $table->string('ip', 15)->default('0.0.0.0');
            $table->timestamps();
            $table->index(array('email_id', 'feed_id'));
            $table->index(array('feed_id', 'email_id'));
            $table->index(array('email_id', 'capture_date'));
            $table->index('capture_date');
        });
    }
}
