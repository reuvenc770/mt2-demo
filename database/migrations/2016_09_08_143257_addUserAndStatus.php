<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserAndStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deploys', function (Blueprint $table) {
           $table->integer("user_id");
            $table->renameColumn("deployed", "deployment_status");
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deploys', function (Blueprint $table) {
            $table->dropColumn("user_id");
            $table->renameColumn("deployment_status","deployed");
        });
    }
}
