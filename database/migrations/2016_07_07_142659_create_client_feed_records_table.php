<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientFeedRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('raw_feed_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email_address')->default('');
            $table->integer('feed_id');
            $table->date('capture_date')->nullable();
            $table->string('source_url');
            // ideally this is stored as an integer using INET_ATON/INET_NTOA
            // but that doesn't work with IPv6 addresses
            // which are not stored as integers, even after being converted (mysql suggests VARBINARY)  
            $table->string('ip'); 

            // these fields are not required
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('address')->default('');
            $table->string('address2')->default('');
            $table->string('city')->default('');
            $table->string('state')->default('');
            $table->string('zip')->default('');
            $table->date('birth_date')->nullable();
            $table->string('gender')->default('');
            $table->string('phone')->default(''); // this is a string elsewhere 
            $table->tinyInteger('valid')->default(1);
            $table->json('extra_fields');

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->index('capture_date', 'capture_date');
            $table->index('created_at', 'created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('raw_feed_records');
    }
}
