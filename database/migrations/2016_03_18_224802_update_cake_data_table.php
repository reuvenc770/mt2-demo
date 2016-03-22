<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCakeDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->table('cake_aggregated_data', function($table) {
            $table->string('subid_2')->after('subid_1')->default('0');
            $table->bigInteger('email_id')->after('subid_2')->default(0);
            $table->date('clickDate')->after('revenue')->default('0000-00-00');
            $table->date('campaignDate')->after('clickDate')->default('0000-00-00');
            $table->dropUnique('s1_s4'); // redundant unique index
            $table->unique(array('subid_1', 'subid_2'), 's1_s2');
            $table->index(array('subid_1', 'email_id'), 'campaign_email');
            $table->index('clickDate', 'clickDate');
            $table->index('campaignDate', 'campaignDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->table('cake_aggregated_data', function($table) {
            $table->dropUnique('s1_s2');
            $table->dropIndex('campaign_email');
            $table->unique(array(
                'subid_1',
                'subid_4',
                ),
                's1_s4'
            );
            $table->dropIndex('clickDate');
            $table->dropIndex('campaignDate');
            $table->dropColumn('subid_2');
            $table->dropColumn('clickDate');
            $table->dropColumn('campaignDate');
        });
        
    }
}
