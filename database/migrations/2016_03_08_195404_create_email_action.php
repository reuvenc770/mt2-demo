<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('emails', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email_address', 100)->default('');
            $table->integer('email_domain_id')->unsigned()->default(0);
            $table->timestamps();
            $table->unique('email_address');
            $table->index('email_domain_id');
        });

        Schema::create('email_domains', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('domain_group_id')->unsigned()->default(0);
            $table->string('domain_name', 40)->default('');
            $table->unique('domain_name');
            $table->index('domain_group_id');
        });

        Schema::create('domain_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->default('');
            $table->tinyInteger('priority')->unsigned()->default(1);
            $table->enum('status', array('Active', 'Paused'));
            $table->unique('name');
        });

        Schema::create('email_client_instances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('client_id')->unsigned()->default(0);
            $table->date('subscribe_date');
            $table->time('subscribe_time');
            $table->date('unsubscribe_date');
            $table->time('unsubscribe_time');
            $table->enum('status', array('A', 'B', 'C', 'U'));
            $table->string('first_name', 20)->default('');
            $table->string('last_name', 40)->default('');
            $table->string('address', 50)->default('');
            $table->string('address2', 50)->default('');
            $table->string('city', 50)->default('');
            $table->char('state', 2);
            $table->string('zip', 10)->default('');
            $table->char('country')->default('');
            $table->date('dob');
            $table->enum('gender', array('', 'M', 'F'))->default('');
            $table->string('phone', 15)->default('');
            $table->string('mobile_phone', 15)->default('');
            $table->string('work_phone', 15)->default('');
            $table->date('capture_date');
            $table->integer('member_source')->default(0);
            $table->string('source_url', 50)->default('');
            $table->string('ip', 15)->default('0.0.0.0');
            $table->timestamps();
            $table->unique(array('email_id', 'client_id', 'capture_date'), 'email_client_date');
            $table->index(array('client_id', 'email_id'));
            $table->index(array('email_id', 'capture_date'));
            $table->index('capture_date');
        });

        /**
         * The tables below will be in the reporting schema/db because the data they contain comes from
         * either the storage tables or from ESPs and will be used for reporting directly.
         */
        Schema::connection('reporting_data')->create('email_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('client_id')->unsigned()->default(0);
            $table->mediumInteger('esp_id')->unsigned()->default(0);
            $table->integer('campaign_id')->unsigned()->default(0);
            $table->tinyInteger('action_id')->unsigned()->default(0);
            $table->dateTime('datetime');
            $table->date('date');
            $table->time('time');
            $table->timestamps();
            // Almost certainly included in the list of keys,
            // But not sure about the rest of them
            $table->index('email_id');
        });

        Schema::connection('reporting_data')->create('action_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', '30');
        });

        Schema::connection('reporting_data')->create('email_campaign_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('campaign_id')->unsigned()->default(0);
            $table->datetime('esp_first_open_datetime');
            $table->datetime('esp_last_open_datetime');
            $table->mediumInteger('esp_total_opens')->unsigned()->default(0);
            $table->datetime('esp_first_click_datetime');
            $table->datetime('esp_last_click_datetime');
            $table->mediumInteger('esp_total_clicks')->unsigned()->default(0);

            $table->datetime('trk_first_open_datetime');
            $table->datetime('trk_last_open_datetime');
            $table->mediumInteger('trk_total_opens')->unsigned()->default(0);
            $table->datetime('trk_first_click_datetime');
            $table->datetime('trk_last_click_datetime');
            $table->mediumInteger('trk_total_clicks')->unsigned()->default(0);

            $table->datetime('mt_first_open_datetime');
            $table->datetime('mt_last_open_datetime');
            $table->mediumInteger('mt_total_opens')->unsigned()->default(0);
            $table->datetime('mt_first_click_datetime');
            $table->datetime('mt_last_click_datetime');
            $table->mediumInteger('mt_total_clicks')->unsigned()->default(0);

            $table->mediumInteger('unsubscribed')->unsigned()->default(0);
            $table->mediumInteger('hard_bounce')->unsigned()->default(0);
            $table->timestamps();

            $table->index(array('campaign_id', 'email_id'));
            $table->index('email_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->drop('email_actions');
        Schema::connection('reporting_data')->drop('action_types');
        Schema::connection('reporting_data')->drop('email_campaign_data');

        Schema::drop('emails');
        Schema::drop('email_domains');
        Schema::drop('domain_groups');
        Schema::drop('email_client_instances');
    }
}
