<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCakeOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cake_offers', function (Blueprint $table) {
            $table->integer('id')->unsigned()->default(0);
            $table->string('name')->default('');
            $table->integer('vertical_id')->default(0);
            $table->integer('cake_advertiser_id')->default(0);

            $table->primary('id');
            $table->index('vertical_id', 'vertical_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('cake_offers');
    }
}
