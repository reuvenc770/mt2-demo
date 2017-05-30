<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatabaseandDimaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $createStatement = 'CREATE DATABASE dima_data';

        if ( env( 'APP_ENV' ) == 'testing' ) {
            $createStatement .= '_test';
        }

        DB::statement( $createStatement );

        Schema::connection("dima_data")->create('maro_raw_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->integer('campaign_id');
            $table->integer('contact_id');
            $table->string('action_type');
            $table->string('browser')->nullable();
            $table->dateTime('recorded_at');
            $table->string("email_address");
            $table->integer("esp_account_id");
            $table->integer("account_number");
            $table->timestamps();
            $table->unique(['contact_id','action_type','recorded_at']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $dropStatement = 'Drop DATABASE dima_data';

        if ( env( 'APP_ENV' ) == 'testing' ) {
            $dropStatement .= '_test';
        }

        DB::statement( $dropStatement );
    }
}
