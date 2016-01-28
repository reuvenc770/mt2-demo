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
            $table->string('account_name');
            $table->integer('campaign_id')->default(0);
            $table->string('name')->default('');
            $table->string('status')->default('');
            $table->integer('sent')->default(0);
            $table->integer('delivered')->default(0);
            $table->integer('open')->default(0);
            $table->integer('click')->default(0);
            $table->integer('bounce')->default(0);
            $table->dateTime('send_at')->default('0000-00-00 00:00:00');
            $table->dateTime('sent_at')->default('0000-00-00 00:00:00');
            $table->dateTime('maro_created_at')->default('0000-00-00 00:00:00');
            $table->dateTime('maro_updated_at')->default('0000-00-00 00:00:00');
            $table->timestamps();
            $table->unique(array('campaign_id', 'sent_at'));
        });

        Schema::table('maro_reports', function($table) {
            $table->foreign('account_name')->references('account_name')->on('esp_accounts');
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
