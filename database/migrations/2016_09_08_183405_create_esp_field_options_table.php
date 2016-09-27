<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEspFieldOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esp_field_options', function (Blueprint $table) {
            $table->integer('esp_id');
            $table->string('email_id_field', 255)->default('');
            $table->string('email_address_field', 255)->default('');
            $table->timestamps();

            $table->primary('esp_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('esp_field_options');
    }
}
