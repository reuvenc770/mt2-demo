<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdditionsToReruns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('deploy_record_reruns', function (Blueprint $table) {
            $table->integer('esp_account_id')->default(0)->after('deploy_id');
            $table->integer('esp_internal_id')->default(0)->after('deploy_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('deploy_record_reruns', function (Blueprint $table) {
            $table->dropColumn('esp_account_id');
            $table->dropColumn('esp_internal_id');
        });
    }
}
