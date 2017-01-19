<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_data', function (Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->default(0);
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
            $table->json('other_fields')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            // indexes required
            $table->primary('email_id');
            $table->index('zip');
            $table->index('city');
            $table->index('state');
            $table->index('gender');
            $table->index('device_type');
            $table->index('device_name');
            $table->index('carrier');
            $table->index('updated_at', 'updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('record_data');
    }
}
