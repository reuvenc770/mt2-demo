<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditTruthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->table('attribution_record_truths', function (Blueprint $table) {
            $table->boolean( 'action_expired' )->default( false )->after("has_action");
            $table->dropIndex("attribution_record_truths_recent_import_index");
            $table->index(["recent_import", "has_action", "action_expired"],"recent_action_expire");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->table('attribution_record_truths', function (Blueprint $table) {
            $table->index( 'recent_import' );
            $table->index( 'has_action' );
            $table->dropIndex("recent_action_expire");
            $table->dropColumn("action_expired");
        });
    }
}
