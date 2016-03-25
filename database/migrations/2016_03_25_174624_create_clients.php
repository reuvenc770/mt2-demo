<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->default('');
            $table->string('address', 100)->default('');
            $table->string('address2', 100)->default('');
            $table->string('city', 100)->default('');
            $table->string('state', 2)->default('');
            $table->string('zip', 10)->default('');
            $table->string('phone', 35)->default('');
            $table->string('email_address', 255)->default('');
            $table->enum('status', ['Active', 'Deleted'])->default('Active');
            $table->string('source_url', 255)->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('clients');
    }
}
