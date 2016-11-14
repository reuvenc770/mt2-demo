<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyFeedReportingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('feed_date_email_breakdowns', function(Blueprint $table) {
            $table->integer('domain_group_id')->unsigned()->default(0)->after('date');
            $table->integer('bad_source_urls')->unsigned()->default(0)->after('cross_feed_duplicates');
            $table->integer('bad_ip_addresses')->unsigned()->default(0)->after('bad_source_urls');
            $table->integer('other_invalid')->unsigned()->default(0)->after('bad_ip_addresses');
            $table->integer('suppressed_domains')->unsigned()->default(0)->after('other_invalid');
            $table->integer('phone_counts')->unsigned()->default(0)->after('cross_feed_duplicates');
            $table->integer('full_postal_counts')->unsigned()->default(0)->after('phone_counts');

            $table->dropIndex('feed_date');
            $table->unique(['feed_id', 'date', 'domain_group_id'], 'feed_date_class');
            $table->renameColumn('fresh_emails', 'unique_emails');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('feed_date_email_breakdowns', function(Blueprint $table) {
            // Down probably can't be run ever.

            $table->dropIndex('feed_date_class');
            $table->dropColumn('domain_group_id');
            $table->dropColumn('bad_source_urls');
            $table->dropColumn('other_invalid');
            $table->dropColumn('bad_ip_addresses');
            $table->dropColumn('suppressed_domains');
            $table->dropColumn('phone_counts');
            $table->dropColumn('full_postal_counts');

            $table->renameColumn('unique_emails', 'fresh_emails');
            $table->unique(['feed_id', 'date'], 'feed_date');

        });
    }
}
