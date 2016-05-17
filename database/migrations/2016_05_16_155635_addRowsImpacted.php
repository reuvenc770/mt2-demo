<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRowsImpacted extends Migration
{
    public function up()
    {
        Schema::table('job_entries', function(Blueprint $table) {
            $table->integer("rows_impacted")->default(0)->after('time_finished');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_entries', function (Blueprint $table) {
            $table->dropColumn("rows_impacted");
        });
    }
}
