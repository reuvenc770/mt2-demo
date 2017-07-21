<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileColumnToRawFeedEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_feed_emails', function(Blueprint $table){
            $table->string('file')->nullable()->after('other_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raw_feed_emails', function(Blueprint $table){
            $table->dropColumn('file');
        });
    }
}
