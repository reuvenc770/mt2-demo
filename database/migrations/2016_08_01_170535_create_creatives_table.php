<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('creatives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->string('file_name')->default('');
            $table->text('creative_html')->default('');
            $table->boolean('approved')->default(0);
            $table->boolean('status')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('creatives');
    }
}
