<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpv6Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ipv6_country_mappings', function(Blueprint $table) {
            $table->bigInteger('first_half_from')->unsigned()->default(0);
            $table->bigInteger('second_half_from')->unsigned()->default(0);
            $table->bigInteger('first_half_to')->unsigned()->default(0);
            $table->bigInteger('second_half_to')->unsigned()->default(0);

            $table->string('country_code', 5)->default('');
            $table->string('country', 20)->default('');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->primary(['first_half_from', 'second_half_from']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('ipv6_country_mappings');
    }
}
