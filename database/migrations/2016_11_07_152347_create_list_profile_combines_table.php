<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListProfileCombinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("list_profile")->create('list_profile_combines', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name", 100);
            $table->integer("list_profile_id")->nullable();
            $table->timestamps();
        });

        Schema::connection("list_profile")->create('list_profile_list_profile_combine', function (Blueprint $table) {
            $table->integer('list_profile_id');
            $table->integer('list_profile_combine_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('list_profile_combines');
        Schema::drop('list_profile_list_profile_combine');
    }
}
