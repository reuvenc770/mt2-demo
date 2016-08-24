<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDbaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doing_business_as', function (Blueprint $table) {
            $table->string("entity_name",50);
            $table->boolean("status");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('doing_business_as', function (Blueprint $table) {
            $table->dropColumn("entity_name");
            $table->dropColumn("status");
        });
    }
}
