<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfusionSoftReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->create('infusion_soft_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('internal_id')->unsigned()->default(0);
            $table->integer('esp_account_id')->unsigned()->default(0);
            $table->string('name')->default('');
            $table->datetime('datetime_created')->nullable();
            $table->string('time_zone')->default('');
            $table->datetime('published_datetime')->nullable();
            $table->string('published_time_zone')->default('');
            $table->string('published_status')->default('');
            $table->integer('active_contact_count')->unsigned()->default(0);
            $table->integer('completed_contact_count')->unsigned()->default(0);
            $table->string('error_message')->default('');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('reporting_data')->drop('infusion_soft_reports');
    }
}
