<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDba extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doing_business_as', function (Blueprint $table) {
            $table->string("phone", 15);
            $table->dropColumn("state_id");
            $table->text("po_boxes");
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
            $table->dropColumn("phone");
            $table->integer("state_id");
            $table->dropColumn("po_boxes");
        });
    }
}
