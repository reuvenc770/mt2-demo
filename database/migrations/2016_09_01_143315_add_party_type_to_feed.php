<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartyTypeToFeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('feeds', function($table) {
            $table->tinyInteger('party')->default(3)->after('name');
            $table->index(['id', 'party'], 'feed_party');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('feeds', function($table) {
            $table->dropColumn('party');
            $table->dropIndex('feed_party');
        });
    }
}
