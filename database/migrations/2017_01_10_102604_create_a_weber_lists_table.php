<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAWeberListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_weber_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('internal_id');
            $table->string("name");
            $table->integer('total_subscribers');
            $table->string("subscribers_collection_link");
            $table->string("campaigns_collection_link");
            $table->boolean("is_active");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_weber_lists');
    }
}
