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
            $table->tinyInteger('offer_payout_type_id')->default(1);
            $table->timestamps();

        });

        // On reflection, the current name makes absolutely no sense
        Schema::connection('attribution')->rename('client_payout_types', 'offer_payout_types');

        // As this table is being repurposed, we need to update one of the fields
        $schema = config('database.connections.attribution.database');
        DB::statement("UPDATE $schema.offer_payout_types SET name = 'CPC' WHERE name = 'Revshare'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $schema = config('database.connections.attribution.database');
        Schema::drop('offers');
        Schema::connection('attribution')->rename('offer_payout_types', 'client_payout_types');
        DB::statement("UPDATE $schema.client_payout_types SET name = 'Revshare' WHERE name = 'CPC'");
    }
}
