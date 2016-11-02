<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSuppressionListIdToFeeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('feeds', function(Blueprint $table) {
            $table->integer('suppression_list_id')->unsigned()->nullable()->default(null)->after('source_url');
            $table->index('suppression_list_id', 'suppression_list_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('feeds', function(Blueprint $table) {
            $table->dropColumn('suppression_list_id');
        });
    }
}
