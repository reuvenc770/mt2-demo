<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::drop('feeds'); // required b/c of the enum

        Schema::create('feeds', function($table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned()->default(0);
            $table->string('name', 100)->default('');
            $table->tinyInteger('party')->unsigned()->default(3);
            $table->string('short_name', 30)->default('');
            $table->enum('status', ['Active', 'Paused', 'Inactive'])->default('Active');
            $table->integer('vertical_id')->unsigned()->default(0);
            $table->enum('frequency', ['RT', 'Daily', 'Weekly', 'Monthly', 'TBD'])->default('TBD');
            $table->integer('type_id');
            $table->integer('country_id')->default(1);
            $table->string('source_url', 255)->default('');
            $table->timestamps();

            $table->index('client_id', 'client_id');
            $table->index('party', 'party');
            $table->index('type_id', 'feed_type');
            $table->index('country_id', 'country');
            $table->index(['id', 'status'], 'feed_status');
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->string('address', 100)->default('');
            $table->string('address2', 100)->default('');
            $table->string('city', 50)->default('');
            $table->string('state', 15)->default('');
            $table->string('zip', 10)->default('');
            $table->string('email_address', 100)->default('');
            $table->string('phone', 50)->default('');
            $table->enum('status', ['Active', 'Paused', 'Inactive'])->default('Active');
            $table->timestamps();

            $table->index(['id', 'status'], 'client_status');
        });

        Schema::create('feed_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('clients');
        Schema::drop('feeds');

        Schema::create('feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->default('');
            $table->tinyInteger('party')->default(3);
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
}
