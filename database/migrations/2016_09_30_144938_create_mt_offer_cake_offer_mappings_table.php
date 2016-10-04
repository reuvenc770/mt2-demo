<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMtOfferCakeOfferMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mt_offer_cake_offer_mappings', function (Blueprint $table) {
            $table->integer('offer_id')->unsigned()->default(0);
            $table->integer('cake_offer_id')->unsigned()->default(0);
            
            $table->index(['offer_id', 'cake_offer_id'], 'mt_cake');
            $table->index(['cake_offer_id', 'offer_id'], 'cake_mt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('mt_offer_cake_offer_mappings');
    }
}
