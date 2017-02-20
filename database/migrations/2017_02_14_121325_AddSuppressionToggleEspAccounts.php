<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSuppressionToggleEspAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esp_accounts', function(Blueprint $table){
            $table->boolean('enable_suppression')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('esp_accounts', function(Blueprint $table){
            $table->dropColumn(['enable_suppression']);
        });
    }
}
