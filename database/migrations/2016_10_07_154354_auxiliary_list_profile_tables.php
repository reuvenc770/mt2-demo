<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuxiliaryListProfileTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('list_profile')->create('list_profile_countries', function(Blueprint $table) {
            $table->integer('list_profile_id')->default(0);
            $table->integer('country_id')->default(0);

            $table->index(['list_profile_id', 'country_id'], 'list_country');
        });


        Schema::create('countries', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->string('abbr')->default('');

        });


        Schema::connection('list_profile')->create('list_profile_clients', function(Blueprint $table) {
            $table->integer('list_profile_id')->default(0);
            $table->integer('client_id')->default(0);

            $table->index(['list_profile_id', 'client_id'], 'list_client');
            $table->index(['client_id', 'list_profile_id'], 'client_list');
        });


        Schema::connection('list_profile')->create('list_profile_feeds', function(Blueprint $table) {
            $table->integer('list_profile_id')->default(0);
            $table->integer('feed_id')->default(0);

            $table->index(['list_profile_id', 'feed_id'], 'list_feed');
            $table->index(['feed_id', 'list_profile_id'], 'feed_list');
        });


        Schema::connection('list_profile')->create('list_profile_domain_groups', function(Blueprint $table) {
            $table->integer('list_profile_id')->default(0);
            $table->integer('domain_group_id')->default(0);

            $table->index(['list_profile_id', 'domain_group_id'], 'list_domain');
            $table->index(['domain_group_id', 'list_profile_id'], 'domain_list');
        });


        Schema::connection('list_profile')->create('list_profile_offers', function(Blueprint $table) {
            $table->integer('list_profile_id')->default(0);
            $table->integer('offer_id')->default(0);

            $table->index(['list_profile_id', 'offer_id'], 'list_offer');
            $table->index(['offer_id', 'list_profile_id'], 'offer_list');
        });


        Schema::connection('list_profile')->create('list_profile_verticals', function(Blueprint $table) {
            $table->integer('list_profile_id')->default(0);
            $table->integer('cake_vertical_id')->default(0);

            $table->index(['list_profile_id', 'cake_vertical_id'], 'list_vertical');
            $table->index(['cake_vertical_id', 'list_profile_id'], 'vertical_list');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('list_profile')->drop('list_profile_countries');
        Schema::drop('countries');
        Schema::connection('list_profile')->drop('list_profile_clients');
        Schema::connection('list_profile')->drop('list_profile_feeds');
        Schema::connection('list_profile')->drop('list_profile_domain_groups');
        Schema::connection('list_profile')->drop('list_profile_offers');
        Schema::connection('list_profile')->drop('list_profile_verticals');
    }
}
