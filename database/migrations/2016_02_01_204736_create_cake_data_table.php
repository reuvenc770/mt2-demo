<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCakeDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cake_aggregated_data', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('advertiser_id')->unsigned()->default(0);
            $table->mediumInteger('affiliate_id')->unsigned()->default(0);
            $table->mediumInteger('offer_id')->unsigned()->default(0);
            $table->mediumInteger('creative_id')->unsigned()->default(0);
            $table->string('subid_1', 100)->default('');
            $table->string('subid_2', 100)->default('');
            $table->string('subid_3', 100)->default('');
            $table->string('subid_4', 100)->default('');
            $table->string('subid_5', 100)->default('');
            $table->date('date')->default('0000-00-00');
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('revenue', 7, 2)->default(0.00);
            $table->timestamps();

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cake_aggregated_data');
    }
}
