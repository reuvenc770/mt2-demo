<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeDeliverableMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('deliverable_csv_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('esp_id')->unsigned()->default(0);
            $table->string('mapping')->default('');

            #$table->tinyInteger('email_pos')->unsigned()->default(0);
            #$table->tinyInteger('eid_pos')->unsigned()->default(0);
            #$table->tinyInteger('campaign_pos')->unsigned()->default(0);
            #$table->tinyInteger('datetime_pos')->unsigned()->default(0);
            $table->timestamps();
            $table->index('esp_id');
        });

        // Drop two un-needed columns
        Schema::connection('reporting_data')->table('email_actions', function($table) {
            $table->dropColumn('date');
            $table->dropColumn('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('deliverable_csv_mappings');

        Schema::connection('reporting_data')->table('email_actions', function ($table) {
            $table->date('date');
            $table->time('time');
        });
    }
}
