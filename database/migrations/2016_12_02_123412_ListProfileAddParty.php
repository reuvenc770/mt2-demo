<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ListProfileAddParty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('list_profile')->table('list_profiles', function(Blueprint $table) {
            $table->tinyInteger('party')->default(3);
        });
        Schema::connection('list_profile')->table('list_profile_combines', function(Blueprint $table) {
            $table->tinyInteger('party')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('list_profile')->table('list_profiles', function(Blueprint $table) {
            $table->dropColumn('party');
        });
        Schema::connection('list_profile')->table('list_profile_combines', function(Blueprint $table) {
            $table->dropColumn('party');
        });
    }
}
