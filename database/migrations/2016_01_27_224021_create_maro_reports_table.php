<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaroReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maro_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_id');
            $table->string('name')->default('');
            $table->string('status')->default('');
            $table->integer('sent')->default(0);
            $table->integer('delivered')->default(0);
            $table->integer('open')->default(0);
            $table->integer('click')->default(0);
            $table->integer('bounce')->default(0);
            $table->dateTime('send_at')->default('0000-00-00 00:00:00');
            $table->dateTime('sent_at')->default('0000-00-00 00:00:00');
            $table->dateTime('created_at')->default('0000-00-00 00:00:00');
            $table->dateTime('updated_at')->default('0000-00-00 00:00:00');
            $table->timestamps()->default('0000-00-00 00:00:00');
            $table->unique(array('campaign_id', 'sent_at'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('maro_reports');
    }
}
