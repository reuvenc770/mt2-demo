<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCakeActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->create('cake_actions', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Cleaned-up information
            $table->integer('email_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->tinyInteger('action_id')->unsigned()->default(1);
            $table->datetime('datetime');
            $table->mediumInteger('esp_account_id')->unsigned()->default(0);

            // And here's all the raw information
            $table->string('subid_1', 100)->default('');
            $table->string('subid_2', 100)->default('');
            $table->string('subid_4', 100)->default('');
            $table->string('subid_5', 100)->default('');
            $table->bigInteger('click_id')->unsigned()->default(0);
            $table->bigInteger('conversion_id')->unsigned()->nullable();
            $table->integer('cake_affiliate_id')->unsigned()->default(0);
            $table->integer('cake_advertiser_id')->unsigned()->default(0);
            $table->integer('cake_offer_id')->unsigned()->default(0);
            $table->integer('cake_creative_id')->unsigned()->default(0);
            $table->integer('cake_campaign_id')->unsigned()->default(0);
            $table->string('ip_address', 30)->default('')->nullable();
            $table->integer('request_session_id')->unsigned()->default(0);
            $table->string('user_agent', 255)->default('');
            $table->decimal('revenue', 7, 2)->default(0.00);

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->unique(['email_id', 'deploy_id', 'action_id', 'datetime'], 'unique_user_action');
            $table->index('deploy_id', 'deploy_id');
            $table->index('action_id', 'action_id');
            $table->index('created_at', 'created_at');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->drop('cake_actions');
    }
}
