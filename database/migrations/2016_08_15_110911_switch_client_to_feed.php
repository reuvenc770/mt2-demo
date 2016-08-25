<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SwitchClientToFeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        //clients to feeds
        Schema::rename('clients', 'feeds');
        
        // email_client_instances to email_feed_instances
        Schema::rename('email_client_instances', 'email_feed_instances');

        // Need raw query because Doctrine does not support ALTER TABLE on tables with ENUM()
        DB::statement("ALTER TABLE email_feed_instances CHANGE `client_id` `feed_id` int(10) unsigned NOT NULL DEFAULT '0'");

        // email client assignments
        Schema::connection('attribution')->rename('email_client_assignments', 'email_feed_assignments');
        Schema::connection('attribution')->table('email_feed_assignments', function($table) {
            $table->renameColumn('client_id', 'feed_id');
        });

        // email client assignment histories
        Schema::connection('attribution')->rename('email_client_assignment_histories', 'email_feed_assignment_histories');
        Schema::connection('attribution')->table('email_feed_assignment_histories', function($table) {
            $table->renameColumn('prev_client_id', 'prev_feed_id');
            $table->renameColumn('new_client_id', 'new_feed_id');
        });

        // client_payouts
        Schema::connection('attribution')->rename('client_payouts', 'offer_payouts');
        Schema::connection('attribution')->table('offer_payouts', function($table) {
            $table->renameColumn('client_id', 'offer_id');
            $table->renameColumn('client_payout_type_id', 'offer_payout_type_id');
        });

        // attribution_levels
        Schema::connection('attribution')->table('attribution_levels', function($table) {
            $table->renameColumn('client_id', 'feed_id');
        });

        Schema::table('temp_stored_emails', function($table) {
            $table->renameColumn('client_id', 'feed_id');
        });

        DB::statement("UPDATE permissions SET name = 'api.feed.index' WHERE name = 'api.client.index'");
        DB::statement("UPDATE permissions SET name = 'api.feed.store' WHERE name = 'api.client.store'");
        DB::statement("UPDATE permissions SET name = 'api.feed.show' WHERE name = 'api.client.show'");
        DB::statement("UPDATE permissions SET name = 'api.feed.update' WHERE name = 'api.client.update'");
        DB::statement("UPDATE permissions SET name = 'api.feed.destroy' WHERE name = 'api.client.destroy'");

        DB::statement("UPDATE permissions SET name = 'feed.list' WHERE name = 'client.list'");
        DB::statement("UPDATE permissions SET name = 'feed.add' WHERE name = 'client.add'");
        DB::statement("UPDATE permissions SET name = 'feed.edit' WHERE name = 'client.edit'");

        DB::statement("UPDATE pages SET name = 'feed.list' WHERE name = 'client.list'");
        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::rename('feeds', 'clients');
        
        Schema::rename('email_feed_instances', 'email_client_instances');
        Schema::connection('attribution')->rename('email_feed_assignments', 'email_client_assignments');
        Schema::connection('attribution')->rename('email_feed_assignment_histories', 'email_client_assignment_histories');
        Schema::connection('attribution')->rename('offer_payouts', 'client_payouts');

        DB::statement("ALTER TABLE email_feed_instances CHANGE `feed_id` `client_id` int(10) unsigned NOT NULL DEFAULT '0'");

        Schema::connection('attribution')->table('email_client_assignment_histories', function($table) {
            $table->renameColumn('prev_client_id', 'prev_feed_id');
            $table->renameColumn('new_feed_id', 'new_feed_id');
        });
        

        Schema::connection('attribution')->table('client_payouts', function($table) {
            $table->renameColumn('offer_id', 'client_id');
            $table->renameColumn('offer_payout_type_id', 'client_payout_type_id');
        });

        Schema::connection('attribution')->table('email_client_assignments', function($table) {
            $table->renameColumn('feed_id', 'client_id');
        });

        Schema::connection('attribution')->table('attribution_levels', function($table) {
            $table->renameColumn('feed_id', 'client_id');
        });

        Schema::table('temp_stored_emails', function($table) {
            $table->renameColumn('feed_id', 'client_id');
        });

        DB::statement("UPDATE permissions SET name = 'api.feed.index' WHERE name = 'api.client.index'");
        DB::statement("UPDATE permissions SET name = 'api.feed.store' WHERE name = 'api.client.store'");
        DB::statement("UPDATE permissions SET name = 'api.feed.show' WHERE name = 'api.client.show'");
        DB::statement("UPDATE permissions SET name = 'api.feed.update' WHERE name = 'api.client.update'");
        DB::statement("UPDATE permissions SET name = 'api.feed.destroy' WHERE name = 'api.client.destroy'");

        DB::statement("UPDATE permissions SET name = 'feed.list' WHERE name = 'client.list'");
        DB::statement("UPDATE permissions SET name = 'feed.add' WHERE name = 'client.add'");
        DB::statement("UPDATE permissions SET name = 'feed.edit' WHERE name = 'client.edit'");

        DB::statement("UPDATE pages SET name = 'feed.list' WHERE name = 'client.list'");
        
    }
}
