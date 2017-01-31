<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNicknameFieldToEspsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esps', function(Blueprint $table){
            $table->string('nickname', 100)->nullable()->after('name');
            $table->unique('nickname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esps', function(Blueprint $table){
            $table->dropColumn('nickname');
        });
    }
}
