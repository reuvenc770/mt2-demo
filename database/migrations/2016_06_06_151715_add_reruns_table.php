<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRerunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('deploy_record_reruns', function (Blueprint $table) {
            $table->bigInteger('deploy_id')->unsigned()->default(0);
            $table->tinyInteger('delivers')->unsigned()->default(0);
            $table->tinyInteger('opens')->unsigned()->default(0);
            $table->tinyInteger('clicks')->unsigned()->default(0);
            $table->tinyInteger('unsubs')->unsigned()->default(0);
            $table->tinyInteger('complaints')->unsigned()->default(0);
            $table->tinyInteger('bounces')->unsigned()->default(0);
            $table->unique('deploy_id', 'deploy_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('deploy_record_reruns');
    }
}
