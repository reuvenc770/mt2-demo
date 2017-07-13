<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileToFeedDateEmailBreakdowns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('feed_date_email_breakdowns', function(Blueprint $table) {
            $table->string('filename', 100)->default('')->after('domain_group_id');
            $table->dropIndex('feed_date_class');
            $table->unique(['feed_id', 'date', 'domain_group_id', 'filename'], 'feed_date_class_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('feed_date_email_breakdowns', function(Blueprint $table) {
            $table->dropColumn('filename');
            $table->dropIndex('feed_date_class_file');
            $table->unique(['feed_id', 'date', 'domain_group_id'], 'feed_date_class');
        });
    }
}
