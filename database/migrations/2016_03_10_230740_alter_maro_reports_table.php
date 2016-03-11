<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMaroReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->table('maro_reports', function ($table) {
            $table->string('from_name')->default('');
            $table->string('from_email')->default('');
            $table->string('subject')->default('');

            $table->integer('unique_opens')->unsigned()->default(0);
            $table->integer('unique_clicks')->unsigned()->default(0);
            $table->integer('unsubscribes')->unsigned()->default(0);
            $table->integer('complaints')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('maro_reports', function($table) {
            $table->dropColumn('from_name');
            $table->dropColumn('from_email');
            $table->dropColumn('subject');
            $table->dropColumn('unique_opens');
            $table->dropColumn('unique_clicks');
            $table->dropColumn('unsubscribes');
            $table->dropColumn('complaints');
        });
    }
}
