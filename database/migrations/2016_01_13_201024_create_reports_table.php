<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('standard_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("internal_id");
            $table->string("account_name");
            $table->string('name');
            $table->string('subject');
            $table->integer('opens');
            $table->integer('clicks');
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
        #standard_reports gets adjusted and moved to the mt2_reports and is dropped elsewhere. This is unnecessary but leaving it here commented out.
        #Schema::drop('standard_reports');
    }
}
