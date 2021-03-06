<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSuppression extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppressions', function(Blueprint $table) {
            $table->dropindex(['campaign_id_esp_account_id_email_address']);
            $table->renameColumn('campaign_id', 'esp_internal_id');
            $table->dropColumn('reason');
            $table->integer('reason_id')->after('date');
            $table->index(['email_address', 'reason_id']);
            $table->index(['esp_internal_id', 'esp_account_id', 'email_address']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppressions', function(Blueprint $table) {
            $table->renameColumn('esp_internal_id' , 'campaign_id');
            $table->index(['campaign_id', 'esp_account_id', 'email_address']);
            $table->string('reason',255);
            $table->dropColumn('reason_id');
        });
    }
}
