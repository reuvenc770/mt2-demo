<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailFeedAttributableDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_attributable_feed_latest_data', function (Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('feed_id')->unsigned()->default(0);
            $table->date('subscribe_date');
            $table->date('capture_date');
            // a rough set of attribution states, mostly for stats. Should rely on email_feed_assignments
            $table->enum('attribution_status', ['POR', 'POA', 'MOA', 'ATTR'])->default('POR'); 
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('address')->default('');
            $table->string('address2')->default('');
            $table->string('city')->default('');
            $table->string('state')->default(''); // more realistically - sub-national administrative unit
            $table->string('zip')->default(''); // postal code?
            $table->string('country')->default(''); // table or list of countries?
            $table->enum('gender', ['M', 'F', 'UNK'])->default('UNK');
            $table->string('ip', 16); // do we want to keep this as a string?
            $table->string('phone'); // do we want a specific format for this?
            $table->string('source_url')->default('');
            $table->date('dob')->nullable();
            $table->string('device_type')->default('');
            $table->string('device_name')->default('');
            $table->string('carrier')->default('');
            $table->json('other_fields')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->primary(['email_id', 'feed_id'], 'email_feed');
            $table->index(['feed_id', 'updated_at'], 'feed_updated');
            $table->index('updated_at', 'updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('email_attributable_feed_latest_data');
    }
}
