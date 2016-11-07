<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveFeedListsToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('suppression')->create('non_third_party_import_suppression_lists', function(Blueprint $table) {
            $table->integer('feed_id')->unsigned()->default(0);
            $table->integer('suppression_list_id')->unsigned()->default(0);

            $table->index('feed_id', 'feed_id');
            $table->index('suppression_list_id', 'suppression_list_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('suppression')->drop('party_suppression_lists');
    }
}
