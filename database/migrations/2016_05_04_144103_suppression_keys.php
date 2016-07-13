<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SuppressionKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppressions', function(Blueprint $table) {
            $table->index('email_address');
            $table->index(['campaign_id', 'esp_account_id', 'email_address']);
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
            $table->dropIndex('suppressions_email_address_index');

            $table->dropIndex('suppressions_campaign_id_esp_account_id_email_address_index');
        });
    }
}
