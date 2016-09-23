<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCakeRedirectDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cake_redirect_domains', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cake_affiliate_id')->default(0);
            $table->integer('offer_payout_type_id')->default(3); // CPA
            $table->string('redirect_domain');
            $table->timestamps();
            
            $table->index(['cake_affiliate_id', 'offer_payout_type_id'], 'affiliate_offer_type');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cake_redirect_domains');
    }
}
