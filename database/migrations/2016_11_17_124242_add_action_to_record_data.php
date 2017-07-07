<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActionToRecordData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        /*Schema::table('record_data', function(Blueprint $table) {
            $table->integer('last_action_offer_id')->nullable()->after('subscribe_date');
            $table->date('last_action_date')->nullable()->after('last_action_offer_id');
            $table->index('last_action_date', 'last_action_date');
            $table->index(['last_action_offer_id', 'last_action_date'], 'last_action');
        });

        Schema::table('first_party_record_data', function(Blueprint $table) {
            $table->integer('last_action_offer_id')->nullable()->after('subscribe_date');
            $table->index('last_action_date', 'last_action_date');
            $table->index(['last_action_offer_id', 'last_action_date'], 'last_action');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
/*
        DB::statement("ALTER TABLE record_data DROP COLUMN `last_action_date`");
        DB::statement("ALTER TABLE record_data DROP COLUMN `last_action_offer_id`");
        DB::statement("ALTER TABLE first_party_record_data DROP COLUMN `last_action_offer_id`");
        DB::statement("ALTER TABLE first_party_record_data DROP INDEX `last_action_date`");*/
    }
}