<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyRegistrar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registrars', function (Blueprint $table) {
            $table->string("contact_name",50);
            $table->string("contact_email",50);
            $table->string("phone_number",15);
            $table->integer("last_cc");
            $table->string("contact_credit_card");
            $table->string("address",100);
            $table->string("address_2",100);
            $table->string("city",100);
            $table->char("state",2);
            $table->integer("zip");
            $table->string("entity_name",100);
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
        Schema::table('registrars', function (Blueprint $table) {
            $table->dropColumn("contact_name");
            $table->dropColumn("contact_email");
            $table->dropColumn("phone_number");
            $table->dropColumn("last_cc");
            $table->dropColumn("contact_credit_card");
            $table->dropColumn("address");
            $table->dropColumn("address_2");
            $table->dropColumn("city");
            $table->dropColumn("state");
            $table->dropColumn("zip");
            $table->dropColumn("entity_name");
            $table->dropColumn("status");
        });
    }
}
