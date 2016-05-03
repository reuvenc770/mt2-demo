<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGhostActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ghost_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string( 'email_address' );
            $table->mediumInteger('esp_account_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->string('action_type');
            $table->dateTime('datetime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ghost_actions');
    }
}
