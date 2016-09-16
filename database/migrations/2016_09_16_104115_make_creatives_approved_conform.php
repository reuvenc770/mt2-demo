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
            $table->renameColumn('approved', 'is_approved');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('creatives', function($table) {
            $table->renameColumn('is_approved', 'approved');
        });
    }
}
