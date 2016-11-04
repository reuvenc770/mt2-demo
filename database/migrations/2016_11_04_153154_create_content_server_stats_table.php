<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentServerStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_server_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->bigInteger('deploy_id')->unsigned()->default(0);
            $table->integer('action_id')->unsigned()->default(0);
            $table->datetime('datetime');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->unique(['email_id', 'deploy_id', 'action_id', 'datetime'], 'email_deploy_action_date');
            $table->index(['deploy_id', 'datetime'], 'deploy_datetime');
            $table->index('datetime', 'datetime');
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
        Schema::drop('content_server_stats');
    }
}
