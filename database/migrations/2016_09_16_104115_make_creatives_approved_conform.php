<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCreativesApprovedConform extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('creatives', function($table) {
            $table->dropColumn('approved');
            $table->boolean('is_approved')->after('creative_html')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('creatives', function($table) {
            $table->dropColumn('is_approved');
            $table->char('approved', 1)->after('creative_html')->default('N');
        });
    }
}
