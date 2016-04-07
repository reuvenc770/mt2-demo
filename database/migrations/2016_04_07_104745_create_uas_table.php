<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('user_agent_strings', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_agent_string', 500)->default('');
            $table->string('browser', 50)->default('');
            $table->string('browser_version', 10)->default('');
            // not strictly device: we have all android devices together
            // ipads and iphones distinct, blackberry, and windows phone
            // with the rest being non-mobile
            $table->string('device', 50)->default('non-mobile device');
            $table->('device_version')
            $table->smallInteger('is_mobile')->default(0);

            $table->unique('user_agent_string', 'user_agent_string');
            $table->index(['is_mobile', 'device'], 'mobile_device');
            $table->index('device', 'device');

        })
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('user_agent_strings');
    }
}
