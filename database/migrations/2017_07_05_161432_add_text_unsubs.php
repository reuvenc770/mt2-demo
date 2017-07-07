<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTextUnsubs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('offers', function($table) {
            $table->text('unsub_text')->default('')->after('exclude_days');
            $table->string('unsub_type', 20)->default('')->after('exclude_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('offers', function($table) {
            $table->dropColumn('unsub_text');
            $table->dropColumn('unsub_type');
        });
    }
}
