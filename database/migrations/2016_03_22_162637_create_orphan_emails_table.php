<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrphanEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orphan_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'email_address' );
            $table->tinyInteger( 'missing_email_record' )->unsigned()->default(0);
            $table->tinyInteger( 'missing_email_client_instance' )->unsigned()->default(0);
            $table->timestamps();
            $table->unique( 'email_address' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orphan_emails');
    }
}
