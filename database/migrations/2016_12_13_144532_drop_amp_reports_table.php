<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAmpReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('amp_reports');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('amp_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'name' );
            $table->integer( 'amp_report_id' )->unsigned();
            $table->timestamps();
        });
    }
}
