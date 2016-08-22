<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReplyEmailToBronto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('bronto_reports', function($table) {
            $table->string('reply_email')->default('')->after('num_social_views');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('bronto_reports', function($table) {
            $table->dropColumn('reply_email');
        });
    }
}
