<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreaseDbaPhoneFieldSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('doing_business_as', function($table) {
            $table->string('phone', 25)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('doing_business_as', function($table) {
            $table->string('phone', 15)->change();
        });
    }
}
