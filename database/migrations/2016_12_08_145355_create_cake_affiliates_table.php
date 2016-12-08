<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCakeAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cake_affiliates', function (Blueprint $table) {
            $table->integer( 'id' )->unsigned();
            $table->string( 'name' );
            $table->timestamps();

            $table->primary( 'id' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cake_affiliates');
    }
}
