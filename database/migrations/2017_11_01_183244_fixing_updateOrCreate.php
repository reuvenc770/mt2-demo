<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use DB;

class FixingUpdateOrCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('domains', function($table) {
            $table->renameColumn('doing_business_as', 'doing_business_as_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('domains', function($table) {
            $table->renameColumn('doing_business_as_id', 'doing_business_as');
        });
    }
}
