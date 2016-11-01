<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferCfsMappingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->create('offer_creative_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offer_id')->default(0);
            $table->integer('creative_id')->default(0);

            $table->unique(['offer_id', 'creative_id'], 'offer_creative');
            $table->index('creative_id', 'creative_id');
        });

        Schema::connection('reporting_data')->create('offer_from_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offer_id')->default(0);
            $table->integer('from_id')->default(0);

            $table->unique(['offer_id', 'from_id'], 'offer_from');
            $table->index('from_id', 'from_id');
        });

        Schema::connection('reporting_data')->create('offer_subject_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offer_id')->default(0);
            $table->integer('subject_id')->default(0);

            $table->unique(['offer_id', 'subject_id'], 'offer_subject');
            $table->index('subject_id', 'subject_id');
        });

        // With the mapping tables, we no longer need these fields on the entity tables

        Schema::table('creatives', function($table) {
            $table->dropIndex('offer_id');
            $table->dropColumn('offer_id');
        });

        Schema::table('froms', function($table) {
            $table->dropIndex('offer_id');
            $table->dropColumn('offer_id');
        });

        Schema::table('subjects', function($table) {
            $table->dropIndex('offer_id');
            $table->dropColumn('offer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->drop('offer_creative_maps');
        Schema::connection('reporting_data')->drop('offer_from_maps');
        Schema::connection('reporting_data')->drop('offer_subject_maps');

        Schema::table('creatives', function($table) {
            $table->integer('offer_id')->default(0)->after('id');
            $table->index('offer_id', 'offer_id');
        });

        Schema::table('froms', function($table) {
            $table->integer('offer_id')->default(0)->after('status');
            $table->index('offer_id', 'offer_id');
        });

        Schema::table('subjects', function($table) {
            $table->integer('offer_id')->default(0)->after('status');
            $table->index('offer_id', 'offer_id');
        });
    }
}