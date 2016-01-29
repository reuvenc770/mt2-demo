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
            $table->integer('esp_id')->default(0);
            $table->integer('esp_account_id')->default(0);
            $table->string('account_name')->default('');
            $table->integer('internal_id')->default(0);
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
            $table->unique(array('internal_id', 'sent_at'));
            $table->index('esp_id');
            $table->index(array('esp_account_id', 'internal_id'));
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
