<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcessedCountProcessFeedFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('processed_feed_files', function(Blueprint $table){
            $table->tinyInteger('processed_count')->unsigned()->nullable()->default( 1 )->after('line_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processed_feed_files', function(Blueprint $table){
            $table->dropColumn('processed_count');
        });
    }
}
