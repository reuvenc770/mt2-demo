<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RedoListProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // Only a few thousand of them. Easy enough to repull.
        Schema::drop('list_profiles');

        Schema::connection('list_profile')->create('list_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->default('');
            $table->integer('deliverable_start')->unsigned()->nullable()->default(null);
            $table->integer("deliverable_end")->unsigned()->nullable()->default(null);
            $table->integer('openers_start')->unsigned()->nullable()->default(null);
            $table->integer('openers_end')->unsigned()->nullable()->default(null);
            $table->integer('open_count')->unsigned()->nullable()->default(null);
            $table->integer('clickers_start')->unsigned()->nullable()->default(null);
            $table->integer('clickers_end')->unsigned()->nullable()->default(null);
            $table->integer('click_count')->unsigned()->nullable()->default(null);
            $table->integer('converters_start')->unsigned()->nullable()->default(null);
            $table->integer('converters_end')->unsigned()->nullable()->default(null);
            $table->integer('conversion_count')->unsigned()->nullable()->default(null);
            $table->json('attributes');
            $table->json('columns');
            $table->enum('run_frequency', ['Daily', 'Weekly', 'Monthly', 'Never'])->default('Daily');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('list_profile')->drop('list_profiles');

        Schema::create('list_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profile_id');
            $table->string("profile_name", 30)->default('');
            $table->integer("opener_start")->default('0');
            $table->integer("opener_end")->default('0');
            $table->integer("clicker_start")->default('0');
            $table->integer("clicker_end")->default('0');
            $table->integer("deliverable_start")->default('0');
            $table->integer("deliverable_end")->default('0');
            $table->tinyInteger("deliverable_factor")->default('0');
            $table->enum("complaint_control", ["Enable","Disable"])->default("Enable");
            $table->integer("cc_aol_send")->default('0');
            $table->integer("cc_yahoo_send")->default('0');
            $table->integer("cc_hotmail_send")->default('0');
            $table->integer("cc_other_send")->default('0');
            $table->char("status")->default('A');
            $table->enum("send_international",['Y','N'])->default('N');
            $table->date("opener_start_date")->nullable();
            $table->date("opener_end_date")->nullable();
            $table->date("clicker_start_date")->nullable();
            $table->date("clicker_end_date")->nullable();
            $table->date("deliverable_start_date")->nullable();
            $table->date("deliverable_end_date")->nullable();
            $table->integer("start_record")->unsigned()->nullable();
            $table->integer("end_record")->unsigned()->nullable();
            $table->tinyInteger("ramp_up_freq")->unsigned()->default('0');
            $table->tinyInteger("subtract_days")->unsigned()->default('0');
            $table->tinyInteger("add_days")->unsigned()->default('0');
            $table->date("last_updated")->nullable();
            $table->integer("max_end_date")->unsigned()->default('180');
            $table->integer("opener_start1")->unsigned()->default('0');
            $table->integer("opener_end1")->unsigned()->default('0');
            $table->integer("clicker_start1")->unsigned()->default('0');
            $table->integer("clicker_end1")->unsigned()->default('0');
            $table->integer("deliverable_start1")->unsigned()->default('0');
            $table->integer("deliverable_end1")->unsigned()->default('0');
            $table->integer("opener_start2")->unsigned()->default('0');
            $table->integer("opener_end2")->unsigned()->default('0');
            $table->integer("clicker_start2")->unsigned()->default('0');
            $table->integer("clicker_end2")->unsigned()->default('0');
            $table->integer("deliverable_start2")->unsigned()->default('0');
            $table->integer("deliverable_end2")->unsigned()->default('0');
            $table->enum("send_confirmed",['Y','N'])->default('Y');
            $table->integer("convert_start")->default('0');
            $table->integer("convert_end")->default('0');
            $table->date("convert_start_date")->nullable();
            $table->date("convert_end_date")->nullable();
            $table->integer("convert_start1")->unsigned()->default('0');
            $table->integer("convert_end1")->unsigned()->default('0');
            $table->integer("convert_start2")->unsigned()->default('0');
            $table->integer("convert_end2")->unsigned()->default('0');
            $table->integer("ramp_up_email_cnt")->unsigned()->default('0');
            $table->enum("gender",['M','F','',"Empty"])->default('');
            $table->tinyInteger("min_age")->default('0');
            $table->tinyInteger("max_age")->default('0');
            $table->string("multitype",255)->default('');
            $table->integer("multi_start")->unsigned()->default('0');
            $table->integer("multi_end")->unsigned()->default('0');
            $table->tinyInteger("multi_cnt")->unsigned()->default('0');
            $table->tinyInteger("DeliveryDays")->unsigned()->default('0');
            $table->enum('DivideRangeByIsps',['Y','N'])->default('N');
            $table->tinyInteger("emailListCnt")->unsigned()->default('0');
            $table->integer("ProfileForClient")->unsigned()->default('0');
            $table->enum("emailListCntOperator",['<','>','='])->default('<');
            $table->integer("groupSuppListID")->unsigned()->default('0');
            $table->enum("useLastCategory",['Y','N'])->default('N');
            $table->enum("BusinessUnit",['Orange','Data'])->default('Orange');
        });
    }
}
