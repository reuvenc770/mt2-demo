<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkflowLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('esp_workflow_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('workflow_id')->unsigned()->default(0);
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->string('target_list', 255)->default('');
            $table->string('status_received', 1000)->default('');
            $table->boolean('binary_status');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->index(['target_list', 'created_at'], 'target_timestamp');
            $table->index(['email_id', 'created_at'], 'email_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('esp_workflow_log');
    }
}
