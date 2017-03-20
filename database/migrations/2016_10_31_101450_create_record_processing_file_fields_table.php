<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordProcessingFileFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_processing_file_fields', function (Blueprint $table) {
            $table->integer( 'feed_id' );
            $table->tinyInteger( 'email_index' )->unsigned();
            $table->tinyInteger( 'source_url_index' )->unsigned();
            $table->tinyInteger( 'capture_date_index' )->unsigned();
            $table->tinyInteger( 'ip_index' )->unsigned();
            $table->tinyInteger( 'first_name_index' )->unsigned()->nullable();
            $table->tinyInteger( 'last_name_index' )->unsigned()->nullable();
            $table->tinyInteger( 'address_index' )->unsigned()->nullable();
            $table->tinyInteger( 'address2_index' )->unsigned()->nullable();
            $table->tinyInteger( 'city_index' )->unsigned()->nullable();
            $table->tinyInteger( 'state_index' )->unsigned()->nullable();
            $table->tinyInteger( 'zip_index' )->unsigned()->nullable();
            $table->tinyInteger( 'country_index' )->unsigned()->nullable();
            $table->tinyInteger( 'gender_index' )->unsigned()->nullable();
            $table->tinyInteger( 'phone_index' )->unsigned()->nullable();
            $table->tinyInteger( 'dob_index' )->unsigned()->nullable();
            if (App::environment('testing')) {
                $table->text('other_field_index')->nullable();
            } else {
                $table->json('other_field_index')->nullable();
            }
            $table->timestamps();

            $table->primary( 'feed_id' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('record_processing_file_fields');
    }
}
