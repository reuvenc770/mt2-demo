<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CountryIDandRemoveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('list_profile')->table('list_profiles', function (Blueprint $table) {
            $table->integer("country_id");
        });
        Schema::connection('list_profile')->drop('list_profile_countries');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('list_profile')->table('list_profiles', function (Blueprint $table) {
            $table->dropColumn("country_id");
        });

        Schema::connection('list_profile')->create('list_profile_countries', function(Blueprint $table) {
            $table->integer('list_profile_id')->default(0);
            $table->integer('country_id')->default(0);

            $table->index(['list_profile_id', 'country_id'], 'list_country');
        });
    }
}
