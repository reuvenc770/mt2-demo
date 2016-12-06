<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartyDeploy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deploys', function(Blueprint $table) {
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
        Schema::table('deploys', function(Blueprint $table) {
            $table->dropColumn('party');
        });

    }
}
