<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatabaseandDimaTables extends Migration
{
    const DIMA_DB_CREATE_STATEMENT = 'CREATE DATABASE %s /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement( sprintf( self::DIMA_DB_CREATE_STATEMENT , $this->getDbName() ) );

        Schema::connection( 'dima_data' )->create('maro_raw_actions', function (Blueprint $table) {
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
        DB::statement( 'Drop DATABASE ' . $this->getDbName() );
    }

    protected function getDbName () {
        $tableName = 'dima_data';

        if ( env( 'APP_ENV' ) == 'testing' ) {
            $tableName .= '_test';
        }

        return $tableName;
    }
}
