<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueKeyToSuppressionLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('suppression')->table('suppression_lists', function($table) {
            $table->unique('name', 'name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('suppression')->table('suppression_lists', function($table) {
            $table->dropUnique('name_unique');
        });
    }
}
