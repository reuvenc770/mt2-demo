<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add2ndCC extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registrars', function (Blueprint $table) {
            $table->string('other_last_cc', 4)->nullable();
            $table->string('other_contact_credit_card', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('registrars', function (Blueprint $table) {
            $table->dropColumn(['other_last_cc,','other_contact_credit_card']);
        });
    }
}
