<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvalidEmailInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invalid_email_instances', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('feed_id')->unsigned()->default(0);
            $table->string('pw')->default('');
            $table->string('email_address')->default('');
            $table->string('source_url')->default('');
            $table->dateTime('capture_date')->nullable();
            $table->string('ip')->default('');
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('address')->default('');
            $table->string('address2')->default('');
            $table->string('city')->default('');
            $table->string('state')->default('');
            $table->string('zip')->default('');
            $table->string('country')->default('');
            $table->string('gender')->default('');
            $table->string('phone')->default('');
            $table->date('dob')->default('');
            $table->json('other_fields');
            $table->string('posting_string', 500)->default('');
            $table->tinyInteger('invalid_reason_id')->unsigned()->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->index('feed_id', 'feed_id');
            $table->index('invalid_reason_id', 'invalid_reason_id');
            $table->index('created_at', 'created_at');
        });

        Schema::create('invalid_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 15);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('invalid_email_instances');
        Schema::drop('invalid_reasons');
    }
}
