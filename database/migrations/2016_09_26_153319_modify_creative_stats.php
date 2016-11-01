<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCreativeStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('creative_clickthrough_rates', function($table) {
            $table->integer('delivers')->default(0)->after('deploy_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('creative_clickthrough_rates', function($table) {
            $table->dropColumn('delivers');
        });
    }
}
