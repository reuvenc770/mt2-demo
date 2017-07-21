<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrevResponderCountToFdeb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('feed_date_email_breakdowns', function($table) {
            $table->integer('prev_responder_count')->unsigned()->default(0)->after('suppressed_domains');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('feed_date_email_breakdowns', function($table) {
            $table->dropColumn('prev_responder_count');
        });
    }
}
