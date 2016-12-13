<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCakeAffiliateIdToProxy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proxies', function(Blueprint $table) {
            $table->integer('cake_affiliate_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proxies', function(Blueprint $table) {
            $table->dropColumn('cake_affiliate_id');
        });
    }
}
