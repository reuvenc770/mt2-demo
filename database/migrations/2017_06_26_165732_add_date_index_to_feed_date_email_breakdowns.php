<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateIndexToFeedDateEmailBreakdowns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('feed_date_email_breakdowns', function($table) {
            $table->index('date', 'date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('feed_date_email_breakdowns', function($table) {
            $table->dropIndex('date');
        });
    }
}
