<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAweberEmailActionsStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->create('a_weber_email_actions_storages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->mediumInteger('esp_account_id')->unsigned()->default(0);
            $table->mediumInteger('esp_internal_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->tinyInteger('action_id')->unsigned()->default(0);
            $table->dateTime('datetime');
            $table->timestamps();
            // Almost certainly included in the list of keys,
            // But not sure about the rest of them
            $table->index('email_id');
            $table->unique(['email_id', 'deploy_id','action_id','datetime'], 'email_deploy_action_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_weber_email_actions_storages');
    }
}
