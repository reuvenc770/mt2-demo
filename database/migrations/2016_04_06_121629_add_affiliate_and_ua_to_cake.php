<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAffiliateAndUaToCake extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('cake_aggregated_data', function($table) {
            $table->string('user_agent_string', 500)->after('subid_5')->default('');
            $table->mediumInteger('affiliate_id')->after('subid_5')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('reporting_data')->table('cake_aggregated_data', function($table) {
            $table->dropColumn('affiliate_id');
            $table->dropColumn('user_agent_string');
        });
    }
}
