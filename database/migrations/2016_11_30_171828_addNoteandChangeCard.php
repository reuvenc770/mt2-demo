<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoteandChangeCard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registrars', function (Blueprint $table) {
            $table->string('last_cc', 4)->change();
            $table->text("notes");
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
            $table->int('last_cc')->change();
            $table->dropColumn("notes");
        });
    }
}
