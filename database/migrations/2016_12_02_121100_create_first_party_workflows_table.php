<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirstPartyWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('esp_workflows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('esp_account_id')->unsigned();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('esp_account_id', 'esp_account_id');
        });

        Schema::create('esp_workflow_steps', function (Blueprint $table) {
            $table->integer('esp_workflow_id')->unsigned();
            $table->integer('step')->unsigned()->default(1);
            $table->integer('deploy_id')->unsigned();
            $table->integer('offer_id')->unsigned()->nullable();
            $table->timestamps();

            $table->primary(['esp_workflow_id', 'step'], 'workflow_step');
            $table->index(['esp_workflow_id', 'deploy_id'], 'workflow_deploy');
            $this->index('offer_id', 'offer_id');
            $this->index('deploy_id', 'deploy_id');
        });

        Schema::create('esp_workflow_feeds', function (Blueprint $table) {
            $table->integer('esp_workflow_id')->unsigned();
            $table->integer('feed_id')->unsigned();

            $table->primary(['esp_workflow_id', 'feed_id'], 'workflow_feed');
            $table->index('feed_id', 'feed_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('esp_workflows');
        Schema::drop('esp_workflow_steps');
        Schema::drop('esp_workflow_feeds');
    }
}
