<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicatorsAccountSuppressionListMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('publicators_suppression_lists', function(Blueprint $table) {
            $table->string('account_name')->default('');
            $table->integer('suppression_list_id')->unsigned()->default(0);
            $table->primary('account_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('publicators_suppression_lists')
    }
}
