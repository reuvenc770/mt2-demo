<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TruthTableNeedsAdditionalImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('attribution')->table('attribution_record_truths', function(Blueprint $table) {
            $table->boolean( 'additional_imports' )->default( false )->after("action_expired");
            $table->dropIndex("recent_action_expire");
            $table->index(["recent_import", "has_action", "action_expired", 'additional_imports'],"recent_action_expire_additional");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('attribution')->table('attribution_record_truths', function(Blueprint $table) {
            $table->dropColumn('additional_imports');
            $table->dropIndex('recent_action_expire_additional');
            $table->index(["recent_import", "has_action", "action_expired"],"recent_action_expire");

        });
    }
}
