<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RedoLpFlatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('list_profile')->drop('list_profile_flat_table');

        Schema::connection('list_profile')->create('list_profile_flat_table', function(Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->smallInteger('esp_account_id')->unsigned()->default(0);
            $table->date('date');
            $table->string('email_address', 100)->default('');
            $table->char('lower_case_md5', 32)->default('');
            $table->char('upper_case_md5', 32)->default('');
            $table->integer('email_domain_id')->unsigned()->default(0);
            $table->integer('email_domain_group_id')->unsigned()->default(0);
            $table->integer('offer_id')->unsigned()->default(0);
            $table->integer('cake_vertical_id')->unsigned()->default(0);

            // New flag fields
            $table->tinyInteger('has_esp_open')->unsigned()->default(0);
            $table->tinyInteger('has_cs_open')->unsigned()->default(0);
            $table->tinyInteger('has_open')->unsigned()->default(0);
            $table->tinyInteger('has_esp_click')->unsigned()->default(0);
            $table->tinyInteger('has_cs_click')->unsigned()->default(0);
            $table->tinyInteger('has_tracking_click')->unsigned()->default(0);
            $table->tinyInteger('has_click')->unsigned()->default(0);
            $table->tinyInteger('has_tracking_conversion')->unsigned()->default(0);
            $table->tinyInteger('has_conversion')->unsigned()->default(0);

            $table->tinyInteger('deliveries')->unsigned()->default(0);
            $table->smallInteger('opens')->unsigned()->default(0);
            $table->smallInteger('clicks')->unsigned()->default(0);
            $table->smallInteger('conversions')->unsigned()->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->unique(['email_id', 'deploy_id', 'date'], 'email_deploy_date');
            $table->index(['email_id', 'date'], 'email_date');
            $table->index(['deploy_id', 'date'], 'deploy_date');
            $table->index('date', 'date');
            $table->index('updated_at', 'updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('list_profile')->drop('list_profile_flat_table');

        Schema::connection('list_profile')->create('list_profile_flat_table', function(Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->date('date');
            $table->string('email_address', 100)->default('');
            $table->integer('email_domain_id')->unsigned()->default(0);
            $table->integer('email_domain_group_id')->unsigned()->default(0);
            $table->integer('offer_id')->unsigned()->default(0);
            $table->integer('cake_vertical_id')->unsigned()->default(0);
            $table->integer('deliveries')->unsigned()->default(0);
            $table->integer('opens')->unsigned()->default(0);
            $table->integer('clicks')->unsigned()->default(0);
            $table->integer('conversions')->unsigned()->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->unique(['email_id', 'deploy_id', 'date'], 'email_deploy_date');
            $table->index(['email_id', 'date'], 'email_date');
            $table->index(['deploy_id', 'date'], 'deploy_date');
            $table->index('date', 'date');
            $table->index('updated_at', 'updated_at');
        });
    }
}
