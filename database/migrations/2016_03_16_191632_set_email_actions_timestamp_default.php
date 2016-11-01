<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetEmailActionsTimestampDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::connection('reporting_data')->statement('ALTER TABLE email_actions MODIFY `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        DB::connection('reporting_data')->statement('ALTER TABLE email_actions MODIFY `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('email_actions', function(Blueprint $table) {
            DB::connection('reporting_data')->statement('ALTER TABLE email_actions DROP COLUMN `created_at`');
            DB::connection('reporting_data')->statement('ALTER TABLE email_actions DROP COLUMN `updated_at`');

            $table->timestamps();
        });
    }
}
