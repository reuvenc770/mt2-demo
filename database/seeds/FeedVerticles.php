<?php

use Illuminate\Database\Seeder;

class FeedVerticles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $verts = ["Auto Insurance","B2B","BizOp","Cash Advance/Payday","Credit Card","Credit Score","Daily Deals","Dating","Debt Settlement","Education","Electronics / Internet","Finance","Gambling","Health and Beauty","Health Insurance","Home Services","Housing","Life Insurance","Medicare Supplements","Miscellaneous","Mortgage","Promo / Survey","Psychic","Senior","Travel"];

        foreach($verts as $vert){
            $verticle = new \App\Models\FeedVertical();
            $verticle->name = $vert;
            $verticle->save();
            unset($verticle);
        }
    }
}
