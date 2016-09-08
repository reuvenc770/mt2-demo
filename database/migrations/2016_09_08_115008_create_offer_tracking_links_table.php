<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferTrackingLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_tracking_links', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offer_id')->default(0);
            $table->integer('link_num')->default(1);
            $table->integer('link_id')->default(0);
            $table->string('url', 500)->default('');
            $table->string('approved_by', 30)->nullable();
            $table->date('date_approved')->nullable();
            $table->timestamps();

            $table->unique(['offer_id', 'link_num'], 'offer_link');
            $table->index('link_id', 'link_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('offer_tracking_links');
    }
}
