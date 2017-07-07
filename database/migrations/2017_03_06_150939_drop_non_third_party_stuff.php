<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropNonThirdPartyStuff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('suppression')->drop('non_third_party_import_suppression_lists');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('suppression')->create('non_third_party_import_suppression_lists', function(Blueprint $table) {
            // creating dummy table
            $table->increments();
        });
    }
}
