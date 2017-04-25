<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeactivationDateToEspAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('esp_accounts', function ($table) {
            $table->date('deactivation_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('esp_accounts', function ($table) {
            $table->dropColumn('deactivation_date');
        });
    }
}
