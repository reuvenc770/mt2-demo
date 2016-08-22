<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableExtensionsForDeploys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // A number of common tables in MT2 need to be extended to include functionality that deploys require

        Schema::table('offers', function($table) {
            $table->string('unsub_link')->default('')->after('offer_payout_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('offers', function($table) {
            $table->dropColumn('unsub_link');
        });
    }
}
