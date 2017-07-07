<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThirdPartyEmailStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_email_statuses', function (Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->enum('last_action_type', ['None', 'Open', 'Click', 'Conversion'])->default('None');
            $table->integer('last_action_offer_id')->nullable()->unsigned()->default(0);
            $table->datetime('last_action_datetime')->nullable();
            $table->integer('last_action_esp_account_id')->nullable()->unsigned()->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->primary('email_id', 'email_id');
            $table->index('last_action_type', 'last_action_type');
            $table->index('updated_at', 'updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('third_party_email_statuses');
    }
}
