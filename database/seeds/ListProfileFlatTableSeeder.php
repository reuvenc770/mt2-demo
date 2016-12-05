<?php

use Illuminate\Database\Seeder;
use App\Models\EtlPickup;

class ListProfileFlatTableSeeder extends Seeder
{

    public function run() {
        $x = new EtlPickup();
        $x->name = 'PopulateListProfileFlatTable';
        $x->stop_point = 1886701849; // Going to start on Aug 1 (~90 days by EOM)
        $x->save();
    }
}