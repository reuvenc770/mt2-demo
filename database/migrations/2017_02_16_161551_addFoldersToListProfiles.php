<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFoldersToListProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("list_profile")->table('list_profiles', function(Blueprint $table){
           $table->string("ftp_folder")->default("lp");
        });

        Schema::connection("list_profile")->table('list_profile_combines', function(Blueprint $table){
            $table->string("ftp_folder")->default("lp_combines");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection("list_profile")->table('list_profiles', function(Blueprint $table){
            $table->dropColumn(["ftp_folder"]);
        });

        Schema::connection("list_profile")->table('list_profile_combines', function(Blueprint $table){
            $table->dropColumn(["ftp_folder"]);
        });
    }
}
