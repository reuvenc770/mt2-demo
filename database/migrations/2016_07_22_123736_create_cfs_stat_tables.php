<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCfsStatTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('mt2_reports')->create('creative_clickthrough_rates', function (Blueprint $table) {
            $table->integer('creative_id')->unsigned()->default(0);
            $table->integer('list_profile_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->integer('opens')->unsigned()->default(0);
            $table->integer('clicks')->unsigned()->default(0);

            $table->primary(['creative_id', 'list_profile_id', 'deploy_id']);
        });

        Schema::connection('mt2_reports')->create('from_open_rates', function (Blueprint $table) {
            $table->integer('from_id')->unsigned()->default(0);
            $table->integer('list_profile_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->integer('delivers')->unsigned()->default(0);
            $table->integer('opens')->unsigned()->default(0);

            $table->primary(['from_id', 'list_profile_id', 'deploy_id']);
        });

        Schema::connection('mt2_reports')->create('subject_open_rates', function (Blueprint $table) {
            $table->integer('subject_id')->unsigned()->default(0);
            $table->integer('list_profile_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->integer('delivers')->unsigned()->default(0);
            $table->integer('opens')->unsigned()->default(0);

            $table->primary(['subject_id', 'list_profile_id', 'deploy_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('mt2_reports')->drop('creative_clickthrough_rates');
        Schema::connection('mt2_reports')->drop('creative_clickthrough_rates');
        Schema::connection('mt2_reports')->drop('creative_clickthrough_rates');
    }
}
