<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixLpOfferSuppression extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('list_profile')->rename('list_profile_offers', 'list_profile_offer_suppression');

        Schema::connection('list_profile')->create('list_profile_offer_actions', function(Blueprint $table) {
            $table->integer('list_profile_id')->default(0);
            $table->integer('offer_id')->default(0);
            $table->index(['list_profile_id', 'offer_id'], 'list_offer');
            $table->index(['offer_id', 'list_profile_id'], 'offer_list');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('list_profile')->rename('list_profile_offers_suppressed', 'list_profile_offer_suppression');
        Schema::connection('list_profile')->drop('list_profile_offer_actions');
    }
}
