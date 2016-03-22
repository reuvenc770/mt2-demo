<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ESPCampaignMappingUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename("esp_account_mappings", "esp_campaign_mappings");

        Schema::table('esp_campaign_mappings', function(Blueprint $table) {
            $table->dropForeign('esp_account_mappings_esp_account_id_foreign');
        });

        Schema::table('esp_campaign_mappings', function (Blueprint $table) {
            $table->integer('esp_id')->unsigned();
            $table->dropColumn("esp_account_id");

        });

        Schema::table('esp_campaign_mappings', function(Blueprint $table) {
            $table->foreign('esp_id')->references('id')->on('esps');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename("esp_campaign_mappings", "esp_account_mappings");
    }
}
