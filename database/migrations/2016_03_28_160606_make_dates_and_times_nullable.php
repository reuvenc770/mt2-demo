<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeDatesAndTimesNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        // reporting data
        Schema::connection('reporting_data')->table('maro_reports', function($table) {
            $table->dateTime('send_at')->nullable()->change();
            $table->dateTime('sent_at')->nullable()->change();
            $table->dateTime('maro_created_at')->nullable()->change();
            $table->dateTime('maro_updated_at')->nullable()->change();
        });

        Schema::connection('reporting_data')->table('cake_aggregated_data', function($table) {
            $table->date('clickDate')->nullable()->change();
        });

        Schema::connection('reporting_data')->table('email_campaign_statistics', function($table) {
            $table->datetime('esp_first_open_datetime')->nullable()->change();
            $table->datetime('esp_last_open_datetime')->nullable()->change();
            $table->datetime('esp_first_click_datetime')->nullable()->change();
            $table->datetime('esp_last_click_datetime')->nullable()->change();
            $table->datetime('trk_first_open_datetime')->nullable()->change();
            $table->datetime('trk_last_open_datetime')->nullable()->change();
            $table->datetime('trk_first_click_datetime')->nullable()->change();
            $table->datetime('trk_last_click_datetime')->nullable()->change();
            $table->datetime('mt_first_open_datetime')->nullable()->change();
            $table->datetime('mt_last_open_datetime')->nullable()->change();
            $table->datetime('mt_first_click_datetime')->nullable()->change();
            $table->datetime('mt_last_click_datetime')->nullable()->change();
        });
        

        // non-reporting 
        
        // workaround required due to Doctrine bug that doesn't allow you to alter 
        // any column in a table that contains an enum. We will likely have to 
        // use raw queries when updating this table in the future 
        // compare to original migration

        Schema::drop('email_client_instances');

        Schema::create('email_client_instances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('client_id')->unsigned()->default(0);
            $table->date('subscribe_datetime')->nullable();
            $table->date('unsubscribe_datetime')->nullable();
            $table->enum('status', array('A', 'B', 'C', 'U'));
            $table->string('first_name', 20)->default('');
            $table->string('last_name', 40)->default('');
            $table->string('address', 50)->default('');
            $table->string('address2', 50)->default('');
            $table->string('city', 50)->default('');
            $table->char('state', 2);
            $table->string('zip', 10)->default('');
            $table->char('country')->default('');
            $table->date('dob')->nullable();
            $table->enum('gender', array('', 'M', 'F'))->default('');
            $table->string('phone', 15)->default('');
            $table->string('mobile_phone', 15)->default('');
            $table->string('work_phone', 15)->default('');
            $table->date('capture_date'); // this really should not be null
            $table->string('source_url', 50)->default('');
            $table->string('ip', 15)->default('0.0.0.0');
            $table->timestamps();
            $table->index(array('email_id', 'client_id'));
            $table->index(array('client_id', 'email_id'));
            $table->index(array('email_id', 'capture_date'));
            $table->index('capture_date');
        });

        Schema::table('temp_stored_emails', function($table) {
            $table->date('capture_date')->nullable()->change();
            $table->date('dob')->nullable()->change();
        });
        
        
        /** 
         *  Other misc changes
         */ 

        
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
