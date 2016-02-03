<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyStandardReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Going to heavily overhaul table. A drop is simpler.
        Schema::drop('standard_reports');

        /**
         *  Some notes on terminology:
         *  m_ - internal ("mailing"): the figures we expected based on MT data
         *  e_ - emailer/esp: data provided by the ESP
         *  t_ - tracking: data provided by the tracking platform (e.g. Cake)
         */

        Schema::create('standard_reports', function (Blueprint $table) {
            $table->increments('id');
            // These below should not be null as it will always be present in the table
            $table->string('deploy_id')->default(''); 
            $table->integer('m_deploy_id')->unsigned()->default(0);
            $table->integer('esp_id')->unsigned()->default(0);
            $table->datetime('datetime')->nullableTimestamps();
            $table->integer('t_creative_id')->unsigned()->default(0);
            $table->integer('m_creative_id')->unsigned()->default(0);
            $table->integer('t_offer_id')->unsigned()->default(0);
            $table->integer('m_offer_id')->unsigned()->default(0);

            // The rows below can be null because they will be updated as data comes in
            $table->string('name')->nullable();
            $table->string('subject')->nullable();
            $table->string('from')->nullable();
            $table->string('from_email')->nullable();
            $table->integer('m_sent')->unsigned()->nullable();
            $table->integer('e_sent')->unsigned()->nullable();
            $table->integer('delivered')->unsigned()->nullable();
            $table->integer('bounced')->unsigned()->nullable();
            $table->integer('optouts')->unsigned()->nullable();
            $table->integer('m_opens')->unsigned()->nullable();
            $table->integer('e_opens')->unsigned()->nullable();
            $table->integer('t_opens')->unsigned()->nullable();
            $table->integer('m_opens_unique')->unsigned()->nullable();
            $table->integer('e_opens_unique')->unsigned()->nullable();
            $table->integer('t_opens_unique')->unsigned()->nullable();
            $table->integer('m_clicks')->unsigned()->nullable();
            $table->integer('e_clicks')->unsigned()->nullable();
            $table->integer('t_clicks')->unsigned()->nullable();
            $table->integer('m_clicks_unique')->unsigned()->nullable();
            $table->integer('e_clicks_unique')->unsigned()->nullable();
            $table->integer('t_clicks_unique')->unsigned()->nullable();
            $table->integer('conversions')->nullable();
            $table->decimal('cost', 7, 2)->nullable();
            $table->decimal('revenue', 7, 2)->nullable();
            $table->timestamps();

            $table->unique('deploy_id');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('standard_reports');
    }
}
