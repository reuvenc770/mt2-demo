<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIPAddressToDimaMaro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("dima_data")->table('maro_raw_actions', function (Blueprint $table) {
            $table->string('ip_address')->after("action_type")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection("dima_data")->table('maro_raw_actions', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });
    }
}
