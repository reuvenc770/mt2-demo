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
        Schema::connection('reporting_data')->create('creative_clickthrough_rates', function (Blueprint $table) {
            $table->integer('creative_id')->unsigned()->default(0);
            $table->integer('list_profile_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->integer('opens')->unsigned()->default(0);
            $table->integer('clicks')->unsigned()->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->primary(['creative_id', 'list_profile_id', 'deploy_id'], 'creative_profile_deploy');
        });

        Schema::connection('reporting_data')->create('from_open_rates', function (Blueprint $table) {
            $table->integer('from_id')->unsigned()->default(0);
            $table->integer('list_profile_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->integer('delivers')->unsigned()->default(0);
            $table->integer('opens')->unsigned()->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->primary(['from_id', 'list_profile_id', 'deploy_id'], 'from_profile_deploy');
        });

        Schema::connection('reporting_data')->create('subject_open_rates', function (Blueprint $table) {
            $table->integer('subject_id')->unsigned()->default(0);
            $table->integer('list_profile_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->integer('delivers')->unsigned()->default(0);
            $table->integer('opens')->unsigned()->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->primary(['subject_id', 'list_profile_id', 'deploy_id'], 'subj_profile_deploy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->drop('creative_clickthrough_rates');
        Schema::connection('reporting_data')->drop('creative_clickthrough_rates');
        Schema::connection('reporting_data')->drop('creative_clickthrough_rates');
    }
}
