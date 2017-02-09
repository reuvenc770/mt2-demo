<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \AdrianMejias\States\States::whereIn("iso_3166_2",['AA','AP','AA','VI','MP','MH','GU','FM','AS','AE'])->delete();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //There is no goin back, this is 'Merica.
    }
}
