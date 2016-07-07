<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('client_feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->string('password', 255)->default('');
            $table->tinyInteger('feed_party_type')->default(1);

            $table->index('client_id', 'client_id');
            $table->index('password', 'password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('client_feeds');
    }
}
