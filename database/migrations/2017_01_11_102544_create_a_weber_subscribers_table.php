<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAWeberSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_weber_subscribers', function (Blueprint $table) {
            $table->increments('id');
            $table->string("email_address");
            $table->integer("internal_id");
            $table->unique(['email_address', "internal_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_weber_subscribers');
    }
}
