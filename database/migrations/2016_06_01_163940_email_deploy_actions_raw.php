<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmailDeployActionsRaw extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->create('raw_email_deploy_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('client_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->mediumInteger('esp_account_id')->unsigned()->default(0);
            $table->integer('esp_internal_id')->unsigned()->default(0);
            $table->tinyInteger('action_id')->unsigned()->default(0);
            $table->datetime('datetime');
            $table->timestamps();
            // Almost certainly included in the list of keys,
            // But not sure about the rest of them
            $table->index('created_at', 'created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
