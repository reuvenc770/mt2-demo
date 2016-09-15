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
            $table->char('exclude_days', 7)->default('NNNNNNN')->after('unsub_link');
        });

        Schema::table('deploys', function ($table) {
            $table->boolean('encrypt_cake')->default(0)->after('cake_affiliate_id');
            $table->boolean('fully_encrypt')->default(0)->after('encrypt_cake');
            $table->string('url_format', 20)->default('new')->after('fully_encrypt');
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
            $table->dropColumn('exclude_days');
        });

        Schema::table('deploys', function($table) {
            $table->dropColumn('encrypt_cake');
            $table->dropColumn('fully_encrypt');
        });
    }
}
