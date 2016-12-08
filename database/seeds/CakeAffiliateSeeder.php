<?php

use Illuminate\Database\Seeder;

use App\Models\CakeAffiliate;

class CakeAffiliateSeeder extends Seeder
{
    protected $affiliates = [
        [ "id" => 211 , "name" => "Levelocity 2" ] ,
        [ "id" => 309 , "name" => "Levelocity" ] ,
        [ "id" => 609 , "name" => "Pegasus" ] ,
        [ "id" => 1010 , "name" => "Orange" ] ,
        [ "id" => 1029 , "name" => "Orange 2" ] ,
        [ "id" => 1391 , "name" => "Gemini" ] ,
        [ "id" => 1454 , "name" => "Pegasus 2" ] ,
        [ "id" => 1455 , "name" => "Pegasus 3" ] ,
        [ "id" => 1479 , "name" => "MSM" ] ,
        [ "id" => 1502 , "name" => "Direct Market" ] ,
        [ "id" => 1512 , "name" => "RealtyTrac" ] ,
        [ "id" => 1521 , "name" => "SimplyJobs" ] ,
        [ "id" => 1540 , "name" => "Red Dealz" ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ( $this->affiliates as $current ) {
            $aff = new CakeAffiliate();
            $aff->id = $current[ 'id' ];
            $aff->name = $current[ 'name' ];
            $aff->save();
        }
    }
}
