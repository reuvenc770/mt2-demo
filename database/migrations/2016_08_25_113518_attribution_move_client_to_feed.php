<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AttributionMoveClientToFeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('attribution')->table('attribution_feed_reports', function($table) {
            $table->renameColumn('client_id', 'feed_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('attribution')->table('attribution_feed_reports', function($table) {
            $table->renameColumn('feed_id', 'client_id');
        });
    }
}
