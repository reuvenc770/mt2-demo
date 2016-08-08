<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenamecakeInstanceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deploys', function (Blueprint $table) {
            $table->renameColumn("cake_instance_id",'list_profile_id');
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
            $table->renameColumn('list_profile_id', "cake_instance_id");
        });
    }
}
