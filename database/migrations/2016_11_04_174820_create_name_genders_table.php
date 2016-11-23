<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNameGendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('name_genders', function (Blueprint $table) {
            $table->string('name');
            $table->enum('gender', ['M', 'F']);
            $table->primary('name');
            $table->index('gender');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('name_genders');
    }
}
