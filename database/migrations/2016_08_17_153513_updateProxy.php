<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProxy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proxies', function (Blueprint $table) {
            $table->text("esp_names")->nullable();
            $table->text("isp_names")->nullable();
            $table->text("notes")->nullable();
            $table->renameColumn("ip_address","ip_addresses");
            $table->dropColumn("domain_type");
            $table->boolean("status");
        });

        Schema::table('proxies', function (Blueprint $table) {
            $table->text("ip_addresses")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proxies', function (Blueprint $table) {
            $table->dropColumn("esp_names");
            $table->dropColumn("isp_names");
            $table->dropColumn("notes");
            $table->renameColumn("ip_addresses","ip_address");
            $table->dropColumn("status");
            $table->tinyInteger("domain_type");
        });

        Schema::table('proxies', function (Blueprint $table) {
            $table->integer("ip_address")->change();
        });
    }
}
