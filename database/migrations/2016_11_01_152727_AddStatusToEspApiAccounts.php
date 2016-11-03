<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToEspApiAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esp_accounts', function(Blueprint $table) {
            $table->boolean('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esp_accounts', function(Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
