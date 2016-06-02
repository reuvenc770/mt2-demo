<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SuppressionReasonMapping extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppression_reasons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("display_status", 255);
            $table->string("legacy_status", 255)->nullable();
            $table->integer("esp_id")->default(0);
            $table->integer('suppression_type')->default(0);
            $table->boolean("display");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('suppression_reasons');
    }


}
