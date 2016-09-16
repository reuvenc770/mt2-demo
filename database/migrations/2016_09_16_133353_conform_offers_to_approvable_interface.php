<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConformOffersToApprovableInterface extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('offers', function($table) {
            $table->boolean('is_approved')->after('name')->default(0);
            $table->char('status', 1)->after('is_approved')->default('I');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('offers', function($table) {
            $table->dropColumn('is_approved');
            $table->dropColumn('status');
        });
    }
}
