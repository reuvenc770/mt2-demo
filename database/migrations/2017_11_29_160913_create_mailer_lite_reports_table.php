<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailerLiteReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->create('mailer_lite_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('internal_id')->unsigned()->default(0);
            $table->integer('esp_account_id')->unsigned()->default(0);
            $table->string('name')->default('');
            $table->string('type')->default('');
            $table->string('status')->default('');
            $table->datetime('datetime_created')->nullable();
            $table->datetime('datetime_send')->nullable();
            $table->integer('total_recipients')->unsigned()->default(0);
            $table->integer('opened')->unsigned()->default(0);
            $table->integer('clicked')->unsigned()->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('reporting_data')->drop('mailer_lite_reports');
    }
}
