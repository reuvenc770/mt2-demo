<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndextoRawDelivers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('reporting_data')->table('raw_delivered_emails', function (Blueprint $table) {
            $table->index("created_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('reporting_data')->table('raw_delivered_emails', function (Blueprint $table) {
            $table->dropIndex("created_at");
        });
    }
}
