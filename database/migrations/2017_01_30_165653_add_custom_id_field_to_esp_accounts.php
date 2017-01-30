<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomIdFieldToEspAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esp_accounts', function(Blueprint $table){
            $table->integer('custom_id')->nullable()->unsigned()->after('account_name');
            $table->unique('custom_id');
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
            $table->dropColumn('custom_id');
        });
    }
}
