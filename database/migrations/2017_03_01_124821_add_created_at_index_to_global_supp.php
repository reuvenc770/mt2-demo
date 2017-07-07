<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedAtIndexToGlobalSupp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('suppression')->table('suppression_global_orange', function ($table) {
            $table->index('created_at', 'created_at');
        });

        Schema::connection('suppression')->table('suppression_list_suppressions', function ($table) {
            $table->index('created_at', 'created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('suppression')->table('suppression_global_orange', function ($table) {
            $table->dropIndex('created_at');
        });

        Schema::connection('suppression')->table('suppression_list_suppressions', function ($table) {
            $table->dropIndex('created_at');
        });
    }
}
