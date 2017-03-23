<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawDeliveredEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->create('raw_delivered_emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->datetime('datetime');
            $table->integer('esp_account_id')->unsigned()->default(0);
            $table->integer('esp_internal_id')->unsigned()->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->drop('raw_delivered_emails');
    }
}
