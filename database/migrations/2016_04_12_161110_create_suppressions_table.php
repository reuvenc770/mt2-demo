<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppressions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("email_address", 150);
            $table->string("reason", 255);
            $table->integer("type_id");
            $table->integer("esp_account_id");
            $table->integer("campaign_id");
            $table->date("date");
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
        Schema::drop('suppressions');
    }
}
