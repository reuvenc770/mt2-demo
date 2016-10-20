<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExpandListProfileSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('list_profile')->table('list_profiles', function(Blueprint $table) {
            $table->dropColumn('attributes');
            $table->boolean('use_global_suppression')->default(1)->after('conversion_count');
            $table->json('feeds_suppressed')->after('use_global_suppression');
            $table->json('age_range')->after('use_global_suppression');
            $table->json('gender')->after('age_range');
            $table->json('zip')->after('gender');
            $table->json('city')->after('zip');
            $table->json('state')->after('city');
            $table->json('device_type')->after('state');
            $table->json('device_os')->after('device_type');
            $table->json('mobile_carrier')->after('device_type');
            $table->boolean('insert_header')->default(0)->after('mobile_carrier');
            $table->integer('total_count')->unsigned()->default(0)->after('insert_header');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('list_profile')->table('list_profiles', function(Blueprint $table) {
            $table->dropColumn('age_range');
            $table->dropColumn('gender');
            $table->dropColumn('zip');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('device_type');
            $table->dropColumn('device_os');
            $table->dropColumn('mobile_carrier');
            $table->dropColumn('insert_header');
            $table->dropColumn('total_count');

            $table->json('attributes')->after('device_type');
        });
    }
}
