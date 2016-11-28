<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ListProfileFeedGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('list_profile')->create('list_profile_feed_groups', function(Blueprint $table) {
            $table->integer('list_profile_id')->default(0);
            $table->integer('feed_group_id')->default(0);

            $table->index(['list_profile_id', 'feed_group_id'], 'list_feed_group');
            $table->index(['feed_group_id', 'list_profile_id'], 'feed_group_list');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('list_profile')->drop('list_profile_feed_groups');
    }
}
