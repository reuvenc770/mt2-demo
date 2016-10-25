<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDeliverableToRecordData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('record_data', function(Blueprint $table) {
            $table->boolean('is_deliverable')->default(1)->after('email_id');
            $table->date('subscribe_date')->after('capture_date');
            $table->index(['email_id', 'is_deliverable', 'subscribe_date'], 'email_status_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('record_data', function(Blueprint $table) {
            $table->dropIndex('email_status_date');
            $table->dropColumn('is_deliverable');
            $table->dropColumn('subscribe_date');
        });
    }
}
