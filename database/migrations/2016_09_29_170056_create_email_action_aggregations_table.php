<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailActionAggregationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // need to create the following first:
        //DB::statement("CREATE DATABASE `list_profile` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */");

        Schema::connection('list_profile')->create('email_action_aggregations', function (Blueprint $table) {

            $table->bigInteger('email_id')->unsigned()->default(0);
            $table->integer('deploy_id')->unsigned()->default(0);
            $table->date('date'); // can't be null but no default specified - want this to fail
            $table->integer('deliveries')->unsigned()->default(0);
            $table->integer('opens')->unsigned()->default(0);
            $table->integer('clicks')->unsigned()->default(0);
            $table->integer('conversions')->unsigned()->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->unique(['email_id', 'deploy_id', 'date'], 'email_deploy_date');
            $table->index('date', 'date');
            $table->index(['email_id', 'date'], 'email_date');
            $table->index(['deploy_id', 'date'], 'deploy_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('list_profile')->drop('email_action_aggregations');
    }
}
