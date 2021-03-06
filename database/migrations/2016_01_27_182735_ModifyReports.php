<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blue_hornet_reports', function($table) {
            $table->integer('esp_account_id')->unsigned();
            $table->dropColumn('account_name');
        });

        Schema::table('blue_hornet_reports', function($table) {
            $table->foreign('esp_account_id')->references('id')->on('esp_accounts');
        });

        Schema::table('campaigner_reports', function($table) {
            $table->integer('esp_account_id')->unsigned();
            $table->dropColumn('account_name');
        });

        Schema::table('campaigner_reports', function($table) {
            $table->foreign('esp_account_id')->references('id')->on('esp_accounts');
        });

        Schema::table('email_direct_reports', function($table) {
            $table->integer('esp_account_id')->unsigned();
            $table->dropColumn('account_name');
        });

        Schema::table('email_direct_reports', function($table) {
            $table->foreign('esp_account_id')->references('id')->on('esp_accounts');
        });

        Schema::table('standard_reports', function($table) {
            $table->integer('esp_account_id')->unsigned();
            $table->dropColumn('account_name');
        });

        Schema::table('standard_reports', function($table) {
            $table->foreign('esp_account_id')->references('id')->on('esp_accounts');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blue_hornet_reports', function($table) {
            $table->dropForeign('blue_hornet_reports_esp_account_id_foreign');
            $table->dropColumn('esp_account_id');
            $table->string("account_name");
        });

        Schema::table('campaigner_reports', function($table) {
            $table->dropForeign('campaigner_reports_esp_account_id_foreign');
            $table->dropColumn('esp_account_id');
            $table->string("account_name");
        });

        Schema::table('email_direct_reports', function($table) {
            $table->dropForeign('email_direct_reports_esp_account_id_foreign');
            $table->dropColumn('esp_account_id');
            $table->string("account_name");
        });
    }
}
