<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultIP extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 3 year old issue.  so doctrine/dbal creates a whole new DoctrineConnection() but Laravels connection is the only one that
        // understands enums, and when creating a new connection is walks the table and cryies about enums...even though im not changing it
        Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::table('feeds', function (Blueprint $table) {
            $table->string('host_ip')->default('52.205.67.250')->change();
        });

        DB::statement("update feeds set host_ip = '52.205.67.250'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::table('feeds', function (Blueprint $table) {
            $table->string('host_ip')->default('52.0.242.68')->change();
        });

        DB::statement("update feeds set host_ip = '52.0.242.68'");
    }
}
