<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SwapBroker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $feedTypes = \App\Models\FeedType::all();
        foreach($feedTypes as $feedType){
            if($feedType->name == "Direct Owner"){
                $feedType->name = "Broker";
                $feedType->save();
            }else if($feedType->name == "Broker"){
                $feedType->name = "Direct Owner";
                $feedType->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $feedTypes = \App\Models\FeedType::all();
        foreach($feedTypes as $feedType){
            if($feedType->name == "Broker"){
                $feedType->name = "Direct Owner";
                $feedType->save();
            }else if($feedType->name == "Direct Owner"){
                $feedType->name = "Broker";
                $feedType->save();
            }
        }
    }
}
