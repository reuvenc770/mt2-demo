<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYmlpReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("reporting_data")->create('ymlp_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('esp_account_id')->unsigned()->default(0);
            $table->integer('internal_id')->default(0);
            $table->string('name')->default(''); // need to find this
            $table->string('from_name')->default('');
            $table->string('from_email')->default('');
            $table->string('subject')->default('');
            $table->datetime('date');
            $table->string('groups')->default('');
            $table->string('filters')->default('');
            $table->integer('recipients')->unsigned()->default(0);
            $table->integer('delivered')->unsigned()->default(0);
            $table->integer('bounced')->unsigned()->default(0);
            $table->integer('total_opens')->unsigned()->default(0);
            $table->integer('unique_opens')->unsigned()->default(0);
            $table->integer('total_clicks')->unsigned()->default(0);
            $table->integer('unique_clicks')->unsigned()->default(0);
            $table->decimal('open_rate', 3, 2)->unsigned()->default(0.00);
            $table->decimal('click_through_rate', 3, 2)->unsigned()->default(0.00);
            $table->string('forwards')->default('');
            $table->string('permalink')->default('');
            $table->timestamps();
            $tableName = env('DB_DATABASE','homestead');
            $table->index('internal_id');
            $table->index(array('esp_account_id', 'internal_id'));
            $table->foreign('esp_account_id')->references('id')->on("{$tableName}.esp_accounts");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ymlp_reports');
    }
}
