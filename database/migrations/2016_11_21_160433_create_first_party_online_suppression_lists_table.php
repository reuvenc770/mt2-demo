<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirstPartyOnlineSuppressionListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('suppression')->create('first_party_online_suppression_lists', function (Blueprint $table) {
            $table->integer('feed_id')->unsigned()->default(0);
            $table->integer('suppression_list_id')->unsigned()->default(0);
            $table->integer('esp_account_id')->unsigned()->default(0);
            $table->string('target_list')->default('');
            
            $table->primary(['feed_id', 'suppression_list_id', 'esp_account_id'], 'feed_list_espaccount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('suppression')->drop('first_party_online_suppression_lists');
    }
}
