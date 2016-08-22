<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixingCfsImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        DB::statement('ALTER TABLE `creatives` MODIFY `status` CHAR(1) NOT NULL DEFAULT "A"');
        DB::statement('ALTER TABLE `creatives` MODIFY `approved` CHAR(1) NOT NULL DEFAULT "N"');
        DB::statement('ALTER TABLE `froms` MODIFY `status` CHAR(1) NOT NULL DEFAULT "A"');
        DB::statement('ALTER TABLE `subjects` MODIFY `status` CHAR(1) NOT NULL DEFAULT "A"');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::statement('ALTER TABLE `creatives` MODIFY `status` TINYINT(1) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `creatives` MODIFY `approved` TINYINT(1) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `froms` MODIFY `status` TINYINT(1) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `subjects` MODIFY `status` TINYINT(1) NOT NULL DEFAULT 0');
    }
}
