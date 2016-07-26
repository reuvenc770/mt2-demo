<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDomainTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proxies', function (Blueprint $table) {
           $table->tinyInteger("domain_type");
        });

        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn("domain_type");
        });
        Schema::table('domains', function (Blueprint $table) {
            $table->tinyInteger("domain_type");
        });

        Schema::table('doing_business_as', function (Blueprint $table) {
            $table->string("registrant_name", 100);
            $table->string("address", 100);
            $table->string("address_2", 100);
            $table->string("city",100);
            $table->string("state",2);
            $table->string("zip",5);
            $table->string("dba_email", 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proxies', function (Blueprint $table) {
            $table->dropColumn("domain_type");
        });

        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn("domain_type");
        });

        Schema::table('domains', function (Blueprint $table) {
            $table->enum("domain_type", ['mailing', 'content']);
        });

        Schema::table('doing_business_as', function (Blueprint $table) {
            $table->dropColumn("registrant_name");
            $table->dropColumn("address");
            $table->dropColumn("address_2");
            $table->dropColumn("city");
            $table->dropColumn("state");
            $table->dropColumn("zip");
            $table->dropColumn("dba_email");
        });

    }
}
