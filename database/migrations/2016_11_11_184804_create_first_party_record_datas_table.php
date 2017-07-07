<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirstPartyRecordDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('first_party_record_data', function (Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('feed_id')->unsigned()->default(0);
            $table->boolean('is_deliverable')->default(1);
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('address')->default('');
            $table->string('address2')->default('');
            $table->string('city')->default('');
            $table->string('state')->default(''); // more realistically - sub state-level conglomeration
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
            $table->date('capture_date');
            $table->date('subscribe_date');
            $table->date('last_action_date')->nullable();
            if (App::environment('testing')) {
                $table->text('other_fields')->nullable();
            } else {
                $table->json('other_fields')->nullable();
            }
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            // indexes required
            $table->primary(['email_id', 'feed_id'], 'email_feed');
            $table->index('feed_id', 'feed_id');
            $table->index('zip');
            $table->index('city');
            $table->index('state');
            $table->index('gender');
            $table->index('device_type');
            $table->index('device_name');
            $table->index('carrier');
            $table->index(['email_id', 'feed_id', 'is_deliverable', 'subscribe_date'], 'email_feed_status_date');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('first_party_record_datas');
    }
}
