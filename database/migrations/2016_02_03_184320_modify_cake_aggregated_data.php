<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCakeAggregatedData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cake_aggregated_data', function($table) {
            $table->dropUnique('adv_aff_off_cre_s1_s2_s3_s4_s5_d');
            $table->dropIndex('cake_aggregated_data_date_index');
            $table->dropColumn('date');
            $table->dropColumn('subid_2');
            $table->dropColumn('subid_3');
            $table->dropColumn('creative_id');
            $table->dropColumn('offer_id');
            $table->dropColumn('advertiser_id');
            $table->dropColumn('affiliate_id');
            $table->unique(array(
                'subid_1',
                'subid_4',
                ),
                's1_s4'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cake_aggregated_data', function($table) {
            $table->dropUnique('s1_s4');
            $table->string('subid_2', 100)->default('');
            $table->string('subid_3', 100)->default('');
            $table->date('date')->default('0000-00-00');
            $table->smallInteger('advertiser_id')->unsigned()->default(0);
            $table->mediumInteger('affiliate_id')->unsigned()->default(0);
            $table->mediumInteger('offer_id')->unsigned()->default(0);
            $table->mediumInteger('creative_id')->unsigned()->default(0);

            $table->unique(array(
                'advertiser_id', 
                'affiliate_id', 
                'offer_id', 
                'creative_id',
                'subid_1',
                'subid_2',
                'subid_3',
                'subid_4',
                'subid_5',
                'date'
                ),
                'adv_aff_off_cre_s1_s2_s3_s4_s5_d'
            );
            $table->index('date');
        });
    }
}
