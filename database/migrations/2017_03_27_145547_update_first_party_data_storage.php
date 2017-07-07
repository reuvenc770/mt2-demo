<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFirstPartyDataStorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('first_party_record_data', function($table) {
            $table->dropColumn('is_deliverable');

            // replaced by the below:
            $table->enum('last_action_type', ['None', 'Open', 'Click', 'Conversion'])->default('None')->after('subscribe_date');
            $table->integer('last_action_offer_id')->unsigned()->default(null)->after('last_action_type')->nullable();
            $table->integer('last_action_esp_account_id')->unsigned()->default(null)->after('last_action_offer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::statement("ALTER TABLE first_party_record_data DROP COLUMN `last_action_type`");
        Schema::table('first_party_record_data', function($table) {
            $table->tinyInteger('is_deliverable')->unsigned()->default(0);
            $table->dropColumn('last_action_offer_id');
            $table->dropColumn('last_action_esp_account_id');
        });
    }
}
