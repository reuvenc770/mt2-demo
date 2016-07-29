<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('offers', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->integer('advertiser_id');
            $table->tinyInt('offer_payout_type_id')->default(1);
            $table->timestamps();
        });

        // On reflection, the current name makes absolutely no sense
        Schema::connection('attribution')->rename('client_payout_types', 'offer_payout_types');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('offers');
    }
}
